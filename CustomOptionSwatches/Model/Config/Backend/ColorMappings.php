<?php
namespace Bodylanguage\CustomOptionSwatches\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\UploaderFactory;
use Magento\Framework\Filesystem;

class ColorMappings extends ArraySerialized
{
    private const UPLOAD_DIR = 'bodylanguage/customoption_images';

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $filesystem
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        UploaderFactory $uploaderFactory,
        Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->filesystem = $filesystem;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Simple file-based debug logger (writes to var/log/customoptionswatches.log).
     *
     * @param string $message
     * @return void
     */
    private function logDebug($message)
    {
        try {
            $logDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR)->getAbsolutePath('log');
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $file = rtrim($logDir, '\/') . '/customoptionswatches.log';
            @file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL, FILE_APPEND);
        } catch (\Throwable $e) {
            // ignore logging failures
        }
    }

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            return parent::beforeSave();
        }

        $uploadedFiles = $this->getUploadedFiles();
        $uploadedFilesIndexed = array_values($uploadedFiles);
        $uploadedPointer = 0;
        $prepared = [];

        foreach ($value as $rowId => $row) {
            if (!is_array($row)) {
                continue;
            }

            $label = isset($row['option_label']) ? trim((string) $row['option_label']) : '';
            $color = isset($row['color']) ? strtoupper(trim((string) $row['color'])) : '';
            $image = $this->extractCurrentImageValue($row);

            // Prefer matching uploaded file by row id, but fall back to sequential matching
            $fileToUse = null;
            if (isset($uploadedFiles[$rowId]) && $uploadedFiles[$rowId] && isset($uploadedFiles[$rowId]['tmp_name']) && $uploadedFiles[$rowId]['tmp_name'] !== '') {
                $fileToUse = $uploadedFiles[$rowId];
            } elseif (isset($uploadedFilesIndexed[$uploadedPointer]) && isset($uploadedFilesIndexed[$uploadedPointer]['tmp_name']) && $uploadedFilesIndexed[$uploadedPointer]['tmp_name'] !== '') {
                $fileToUse = $uploadedFilesIndexed[$uploadedPointer];
                $uploadedPointer++;
            }

            if ($fileToUse !== null) {
                $image = $this->uploadImage($fileToUse);

                // Mark this uploaded tmp file as consumed so it isn't moved twice.
                $tmpUsed = isset($fileToUse['tmp_name']) ? (string)$fileToUse['tmp_name'] : null;
                if ($tmpUsed !== null) {
                    // Remove any matching entries from the indexed list
                    foreach ($uploadedFilesIndexed as $k => $entry) {
                        if (isset($entry['tmp_name']) && (string)$entry['tmp_name'] === $tmpUsed) {
                            unset($uploadedFilesIndexed[$k]);
                        }
                    }
                    // Also remove from associative map if present
                    foreach ($uploadedFiles as $k => $entry) {
                        if (isset($entry['tmp_name']) && (string)$entry['tmp_name'] === $tmpUsed) {
                            unset($uploadedFiles[$k]);
                        }
                    }
                    // Reindex the indexed array to keep pointer logic sane
                    $uploadedFilesIndexed = array_values($uploadedFilesIndexed);
                }
            }

            if ($color !== '' && !preg_match('/^#[0-9A-F]{6}$/', $color)) {
                throw new LocalizedException(
                    __('Each swatch color must be a valid 6-digit hex code like #FF00AA.')
                );
            }

            if ($label === '' || ($color === '' && $image === '')) {
                continue;
            }

            $preparedRow = ['option_label' => $label];
            if ($color !== '') {
                $preparedRow['color'] = $color;
            }
            if ($image !== '') {
                $preparedRow['image'] = $image;
            }

            $prepared[] = $preparedRow;
        }

        $this->setValue($prepared);

        return parent::beforeSave();
    }

    /**
     * @return array
     */
    private function getUploadedFiles()
    {
        // phpcs:ignore Magento2.Security.Superglobal
        if (!isset($_FILES['groups']) || !is_array($_FILES['groups'])) {
            return [];
        }

        // Log raw $_FILES['groups'] for debugging
        $this->logDebug('Raw $_FILES[groups]: ' . str_replace("\n", ' ', print_r($_FILES['groups'], true)));

        $files = $this->normalizeFilesArray($_FILES['groups']);
        // Debug log normalized files structure (do not log file contents)
        $this->logDebug('Normalized $_FILES[groups] keys: ' . str_replace("\n", ' ', print_r(array_map(function ($v) {
            return is_array($v) ? array_keys($v) : $v;
        }, $files), true)));
        $this->logDebug('Normalized $_FILES[groups] full: ' . str_replace("\n", ' ', print_r($files, true)));
        $path = explode('/', $this->getPath());
        $field = array_pop($path);

        $this->logDebug('Config path: ' . $this->getPath() . ' target field=' . $field . ' remaining path=' . implode('/', $path));

        // Search the normalized files tree for the branch that contains our field
        $branch = $this->findFieldBranch($files, $field);
        if ($branch === null) {
            $this->logDebug('Could not locate uploaded files branch for field ' . $field);
            return [];
        }

        if (!isset($branch['fields'][$field]['value']) || !is_array($branch['fields'][$field]['value'])) {
            $this->logDebug('Located branch but no value array at fields.' . $field . '.value');
            return [];
        }

        $result = [];
        foreach ($branch['fields'][$field]['value'] as $rowId => $rowData) {
            if (isset($rowData['image']['file']) && is_array($rowData['image']['file'])) {
                $result[$rowId] = $rowData['image']['file'];
            }
        }

        $this->logDebug('Extracted uploaded files for field ' . $field . ': ' . json_encode(array_map(function ($f) {
            return isset($f['tmp_name']) ? $f['tmp_name'] : null;
        }, $result)));

        return $result;
    }

    /**
     * Recursively search normalized files array for a branch that contains the given field
     * under a 'fields' key.
     *
     * @param array $files
     * @param string $field
     * @return array|null
     */
    private function findFieldBranch(array $files, $field)
    {
        foreach ($files as $key => $value) {
            if (!is_array($value)) {
                continue;
            }

            if (isset($value['fields']) && isset($value['fields'][$field])) {
                return $value;
            }

            // Recurse into nested arrays
            foreach ($value as $child) {
                if (is_array($child)) {
                    $found = $this->findFieldBranch($child, $field);
                    if ($found !== null) {
                        return $found;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param mixed $row
     * @return string
     */
    private function extractCurrentImageValue($row)
    {
        if (!isset($row['image'])) {
            return '';
        }

        if (is_array($row['image']) && isset($row['image']['value'])) {
            return trim((string) $row['image']['value']);
        }

        return trim((string) $row['image']);
    }

    /**
     * @param array $file
     * @return string
     */
    private function uploadImage(array $file)
    {
        $uploadDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath(self::UPLOAD_DIR);
        try {
            $tmpName = isset($file['tmp_name']) ? (string) $file['tmp_name'] : '';
            if ($tmpName === '') {
                $this->logDebug('Upload skipped: tmp_name missing: ' . var_export($file, true));
                throw new LocalizedException(__('Uploaded temporary file not found.'));
            }

            // Fallback to moving the uploaded file directly to media directory to avoid Uploader validation issues
            $originalName = isset($file['name']) ? (string) $file['name'] : basename((string)($file['full_path'] ?? 'upload'));
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $extension = $extension ? strtolower($extension) : '';
            $allowed = ['jpg', 'jpeg', 'gif', 'png'];
            if ($extension !== '' && !in_array($extension, $allowed, true)) {
                throw new LocalizedException(__('Invalid file extension.'));
            }

            $uniqueName = 'swatch_' . uniqid() . ($extension ? '.' . $extension : '');
            $destPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $uniqueName;

            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $this->logDebug('Attempting to move uploaded file. tmp=' . $tmpName . ' is_uploaded_file=' . (is_uploaded_file($tmpName) ? '1' : '0'));

            $moved = false;
            // Prefer move_uploaded_file when possible
            if (is_uploaded_file($tmpName)) {
                $moved = @move_uploaded_file($tmpName, $destPath);
                $this->logDebug('move_uploaded_file result: ' . ($moved ? 'true' : 'false'));
            }

            if (!$moved) {
                // try rename as a fallback
                $moved = @rename($tmpName, $destPath);
                $this->logDebug('rename result: ' . ($moved ? 'true' : 'false'));
            }

            if (!$moved) {
                $this->logDebug('Failed to move uploaded file from ' . $tmpName . ' to ' . $destPath . ' (exists=' . (file_exists($tmpName) ? '1' : '0') . ')');
                throw new LocalizedException(__('Failed to save uploaded file.'));
            }

            // Ensure permissions
            @chmod($destPath, 0644);

            return self::UPLOAD_DIR . '/' . ltrim($uniqueName, '/');
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->logDebug('Upload exception: ' . $e->getMessage());
            throw new LocalizedException(__('%1', $e->getMessage()));
        }
    }

    /**
     * @param array $files
     * @param string $key
     * @param string $param
     * @return void
     */
    private function normalizeFilesRecursive(array &$files, $value, $param, array $keys = [])
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $this->normalizeFilesRecursive($files, $item, $param, array_merge($keys, [$key]));
            }
            return;
        }

        $ref = &$files;
        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }
            $ref = &$ref[$key];
        }
        $ref[$param] = $value;
    }

    /**
     * @param array $data
     * @return array
     */
    private function normalizeFilesArray(array $data)
    {
        $result = [];
        foreach ($data as $param => $value) {
            $this->normalizeFilesRecursive($result, $value, $param);
        }
        return $result;
    }
}

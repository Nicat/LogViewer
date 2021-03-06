<?php
/**
 * Class LogViewer | app/Models/LogViewer.php
 *
 * @package     app\Models
 * @subpackage  Categories
 * @author      Nijat Asadov <nijatasdaov@gmail.com>
 * @version     v.1.0.0 (15/06/2017)
 * @copyright   Copyright (c) 2017, Nijat Asadov
 */

namespace app\Models;

use phpFastCache\CacheManager;

/**
 * Class LogViewer
 */
class LogViewer
{
    /**
     * @var string Version
     */
    private $version = '1.0.0';

    /**
     * @var string $_path Path of file
     * @var int $_page Pagination
     * @var int $_limit How many lines to show
     */
    protected $_path, $_page, $_limit;

    /**
     * @var array Lines of file
     */
    protected $_result = [];

    /**
     * @var bool Checking cache status
     * @see http://www.phpfastcache.com/
     */
    protected $cache = true;

    /**
     * LogViewer constructor.
     *
     * @param string|null $path
     * @param int $page
     * @param int $limit
     */
    function __construct(string $path = null, int $page = 0, int $limit = 10)
    {
        $this->_path = urldecode($path);
        $this->_page = $page;
        $this->_limit = $limit;
    }

    /**
     * Read Lines of File
     *
     * @param bool $last
     * @return string
     */
    public function lines(bool $last = false)
    {
        $start = microtime(true);

        $path = $this->_path;
        $page = $this->_page;
        $limit = $this->_limit;

        $extra = 0;
        $seek = $page * $limit;
        $end = $limit + $seek - 1;

        /* Caching key */
        if ($this->isCache()) {
            $cache = @CacheManager::getInstance('files');
            $key = "search_" . md5($path . "_" . ($last ? 'last' : ($page . "_" . $limit)));
            $cacheItem = $cache->getItem($key);
            $cacheResult = $cacheItem->get();
        } else {
            $cacheResult = null;
        }

        if (is_null($cacheResult)) {
            if (is_file($path)) { /* File */
                if (! is_readable($path)) {
                    $result = [
                        'error' => "File isn't readable",
                    ];
                } else {
                    $result = [
                        'time' => 0,
                        'cache' => false,
                        'type' => 'file',
                        'prev' => boolval($page),
                        'current' => false,
                        'next' => false,
                    ];
                    if ($last) {
                        $handle = $this->tail($path);
                        $lines = $this->countLines($path);
                        $extra = max(0, $lines - $limit);
                        $result['prev'] = null;
                        $result['page'] = floor($lines / $limit) - 1;
                    } else {
                        $handle = fopen($path, 'r');
                    }

                    if ($handle) {
                        $line = 0;
                        while (($buffer = fgets($handle, 4096)) !== false) {
                            /* Seeking */
                            if ($line >= $seek) {
                                /* End of reading */
                                if ($line > $end) {
                                    if (! $page) {
                                        if ($line > $end + $limit) {
                                            break;
                                        }
                                        if (! $result['next']) {
                                            $result['next'] = [];
                                        }
                                        array_push($result['next'], [
                                            'line' => $line + 1 + $extra,
                                            'text' => utf8_encode($buffer),
                                        ]);
                                    } else {
                                        $result['next'] = true;
                                        break;
                                    }
                                } else {
                                    if (! $result['current']) {
                                        $result['current'] = [];
                                    }
                                    array_push($result['current'], [
                                        'line' => $line + 1 + $extra,
                                        'text' => utf8_encode($buffer),
                                    ]);
                                }
                            }
                            $line++;
                        }
                        fclose($handle);
                    }
                }
            } else { /* Undefined */
                $result = [
                    'error' => 'This is not file',
                ];
            }

            if ($this->isCache()) {
                /* Caching result for 2 minutes */
                $cacheItem->set($result)->expiresAfter(120);
                $cache->save($cacheItem);
            } else {
                $result['cache'] = false;
            }
        } else {
            $result = $cacheResult;
            $result['cache'] = true;
        }

        $result['time'] = round(microtime(true) - $start, 5);

        $this->_result = $result;

        return $this;
    }

    /**
     * Next page
     *
     * @return string
     */
    public function nextPage()
    {
        $this->_page++;

        return $this->lines();
    }

    /**
     * Prev Page
     *
     * @return string
     */
    public function prevPage()
    {
        $this->_page--;

        return $this->lines();
    }

    /**
     * Last lines of file
     *
     * @return string
     */
    public function lastPage()
    {
        return $this->lines(true);
    }

    /**
     * Return lines as json
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->_result, JSON_FORCE_OBJECT);
    }

    /**
     * Return lines as array
     *
     * @return array
     */
    public function get()
    {
        return $this->_result;
    }

    /**
     * Count Lines of File
     *
     * @param string $path
     * @return int
     */
    public function countLines(string $path): int
    {
        return intval(exec('grep -c $ ' . $path));
    }

    /**
     * Tail File
     *
     * @param string $path
     * @return bool|resource
     */
    public function tail(string $path)
    {
        return popen('tail -n ' . $this->_limit . ' ' . $path, "r");
    }

    /**
     * Check caching is activated or not
     *
     * @return bool
     */
    public function isCache(): bool
    {
        return $this->cache;
    }

    /**
     * Set Caching
     *
     * @param bool $cache
     * @return $this
     */
    public function setCache(bool $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Get current file path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->_path;
    }

    /**
     * Set new file path
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->_path = $path;

        return $this;
    }

    /**
     * Get Current page
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->_page;
    }

    /**
     * Set current page
     *
     * @param int $page
     * @return $this
     */
    public function setPage(int $page)
    {
        $this->_page = $page;

        return $this;
    }

    /**
     * Get lines limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->_limit;
    }

    /**
     * Set Lines limit
     *
     * @param int $limit
     * @return $this
     */
    public function setLimit(int $limit)
    {
        $this->_limit = $limit;

        return $this;
    }

    /**
     * Version of Class
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Elasticsearch\SearchAdapter\Query\Preprocessor;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\App\Cache\Type\Config as ConfigCache;
use Magento\Elasticsearch\Model\Adapter\Index\Config\EsConfigInterface;
use Magento\Framework\Search\Adapter\Preprocessor\PreprocessorInterface;
use Magento\Framework\Module\Dir\Reader as ModuleDirReader;
use Magento\Framework\Module\Dir;

class Stopwords implements PreprocessorInterface
{
    /**
     * Cache id for elasticsearch stopwords
     */
    const CACHE_ID = 'elasticsearch_stopwords';

    /**
     * Stopwords file modification time gap, seconds
     */
    const STOPWORDS_FILE_MODIFICATION_TIME_GAP = 900;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LocaleResolver
     */
    protected $localeResolver;

    /**
     * @var ReadFactory
     */
    protected $readFactory;

    /**
     * @var ConfigCache
     */
    protected $configCache;

    /**
     * @var EsConfigInterface
     */
    protected $esConfig;

    /**
     * @var ModuleDirReader
     */
    protected $moduleDirReader;

    /**
     * @var string
     */
    private $stopwordsModule;

    /**
     * @var string
     */
    private $stopwordsDirectory;

    /**
     * Initialize dependencies.
     *
     * @param StoreManagerInterface $storeManager
     * @param LocaleResolver $localeResolver
     * @param ReadFactory $readFactory
     * @param ConfigCache $configCache
     * @param EsConfigInterface $esConfig
     * @param ModuleDirReader $moduleDirReader
     * @param string $stopwordsModule
     * @param string $stopwordsDirectory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        LocaleResolver $localeResolver,
        ReadFactory $readFactory,
        ConfigCache $configCache,
        EsConfigInterface $esConfig,
        ModuleDirReader $moduleDirReader,
        $stopwordsModule = '',
        $stopwordsDirectory = ''
    ) {
        $this->storeManager = $storeManager;
        $this->localeResolver = $localeResolver;
        $this->readFactory = $readFactory;
        $this->configCache = $configCache;
        $this->esConfig = $esConfig;
        $this->moduleDirReader = $moduleDirReader;
        $this->stopwordsModule = $stopwordsModule;
        $this->stopwordsDirectory = $stopwordsDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function process($query)
    {
        $stopwords = $this->getStopwordsList();
        $queryParts = explode(' ', $query);
        $query = implode(' ', array_diff($queryParts, $stopwords));
        return trim($query);
    }

    /**
     * Get stopwords list for current locale
     *
     * @return array
     */
    protected function getStopwordsList()
    {
        $filename = $this->getStopwordsFile();
        $fileDir = $this->moduleDirReader->getModuleDir(Dir::MODULE_ETC_DIR, $this->stopwordsModule)
            . '/' . $this->stopwordsDirectory;
        $source = $this->readFactory->create($fileDir);
        $fileStats = $source->stat($filename);
        if (((time() - $fileStats['mtime']) > self::STOPWORDS_FILE_MODIFICATION_TIME_GAP)
            && ($cachedValue = $this->configCache->load(self::CACHE_ID))) {
            $stopwords = unserialize($cachedValue);
        } else {
            $fileContent = $source->readFile($filename);
            $stopwords = explode("\n", $fileContent);
            $this->configCache->save(serialize($stopwords), self::CACHE_ID);
        }
        return $stopwords;
    }

    /**
     * Get stopwords file for current locale
     *
     * @return string
     */
    protected function getStopwordsFile()
    {
        $stopwordsInfo = $this->esConfig->getStopwordsInfo();
        $storeId = $this->storeManager->getStore()->getId();
        $this->localeResolver->emulate($storeId);
        $locale = $this->localeResolver->getLocale();
        $stopwordsFile = isset($stopwordsInfo[$locale]) ? $stopwordsInfo[$locale] : $stopwordsInfo['default'];
        return $stopwordsFile;
    }
}

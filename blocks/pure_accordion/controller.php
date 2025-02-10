<?php

namespace Concrete\Package\PureAccordion\Block\PureAccordion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController implements FileTrackableInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btTable
     */
    protected $btTable = 'btPureAccordion';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportContentColumns
     */
    protected $btExportContentColumns = ['content'];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceWidth
     */
    protected $btInterfaceWidth = 600;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceHeight
     */
    protected $btInterfaceHeight = 465;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutput
     */
    protected $btCacheBlockOutput = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutputOnPost
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$supportSavingNullValues
     */
    protected $supportSavingNullValues = true;

    /**
     * @var string|null
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $handle;

    /**
     * @var string|null
     */
    protected $content;

    /**
     * @var bool|int|string|null
     */
    protected $openedByDefault;

    /**
     * @var bool|int|string|null
     */
    protected $showPermalink;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeName()
     */
    public function getBlockTypeName()
    {
        return t('Pure Accordion');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeDescription()
     */
    public function getBlockTypeDescription()
    {
        return t('Simple accordion with permalinks');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::registerViewAssets()
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('font-awesome');
    }

    public function add()
    {
        $this->set('title', '');
        $this->set('handle', '');
        $this->set('content', '');
        $this->set('openedByDefault', false);
        $this->set('showPermalink', true);
        $this->prepareEdit();
    }

    public function edit()
    {
        $this->set('content', LinkAbstractor::translateFromEditMode($this->content));
        $this->prepareEdit();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::validate()
     */
    public function validate($args)
    {
        $check = $this->normalizeArgs($args);

        return is_array($check) ? true : $check;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::save()
     */
    public function save($args)
    {
        $normalized = $this->normalizeArgs($args);
        if (!is_array($normalized)) {
            throw new UserMessageException(implode("\n", $normalized->getList()));
        }

        return parent::save($normalized);
    }

    public function view()
    {
        $this->set('content', LinkAbstractor::translateFrom($this->content));
        $this->set('identifier', $this->app->make(Identifier::class));
        $currentPage = Page::getCurrentPage();
        $this->set('currentPage', $currentPage && !$currentPage->isError() ? $currentPage : null);
        $this->set('currentPageUrl', $currentPage ? $this->app->make(ResolverManagerInterface::class)->resolve([$currentPage]) : '');
    }

    /**
     * @return string
     */
    public function getSearchableContent()
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        return array_merge(
            $this->getUsedFilesImages(),
            $this->getUsedFilesDownload()
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedCollection()
     */
    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    protected function prepareEdit()
    {
        $this->set('editor', $this->app->make('editor'));
    }

    /**
     * @return \Concrete\Core\Error\Error|\Concrete\Core\Error\ErrorList\ErrorList|array
     */
    protected function normalizeArgs($args)
    {
        if (!is_array($args)) {
            $args = [];
        }
        $normalized = [];
        $e = $this->app->make('helper/validation/error');
        $normalized['handle'] = isset($args['handle']) && is_string($args['handle']) ? trim($args['handle']) : '';
        $normalized['title'] = isset($args['title']) && is_string($args['title']) ? trim($args['title']) : '';
        $len = function_exists('mb_strlen') ? mb_strlen($normalized['title']) : strlen($normalized['title']);
        if ($len === 0) {
            $e->add(t('%s is required', 'Title'));
        } elseif ($len > 255) {
            $e->add(t('%1$s must be shorter than %2$s characters', 'Title', 255));
        } else {
            if ($normalized['handle'] === '') {
                $th = $this->app->make('helper/text');
                $normalized['handle'] = $th->shortText($th->urlify($normalized['title']), 240, '') . ($this->bID ? "-{$this->bID}" : '');
            }
        }
        $normalized['content'] = isset($args['content']) && is_string($args['content']) ? trim($args['content']) : '';
        if ($normalized['content'] === '') {
            $e->add(t('%s is required', 'Content'));
        } else {
            $normalized['content'] = LinkAbstractor::translateTo($normalized['content']);
        }
        foreach (['openedByDefault', 'showPermalink'] as $field) {
            if (!isset($args[$field])) {
                $normalized[$field] = 0;
            } else {
                $normalized[$field] = filter_var($args[$field], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
        }

        return $e->has() ? $e : $normalized;
    }

    /**
     * @return int[]|string[]
     */
    protected function getUsedFilesImages()
    {
        if (!$this->content) {
            return [];
        }
        $files = [];
        $matches = [];
        if (preg_match_all('/\<concrete-picture[^>]*?fID\s*=\s*[\'"]([^\'"]*?)[\'"]/i', (string) $this->content, $matches)) {
            list(, $ids) = $matches;
            foreach ($ids as $id) {
                $files[] = $id;
            }
        }

        return $files;
    }

    /**
     * @return int[]|string[]
     */
    protected function getUsedFilesDownload()
    {
        if (!$this->content) {
            return [];
        }
        $matches = [];
        if (!preg_match_all('(FID_DL_\d+)', $this->content, $matches)) {
            return [];
        }

        return array_map(
            static function ($match) {
                return explode('_', $match)[2];
            },
            $matches[0]
        );
    }
}

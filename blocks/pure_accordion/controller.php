<?php

namespace Concrete\Package\PureAccordion\Block\PureAccordion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Page\Page;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Utility\Service\Xml;
use SimpleXMLElement;

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
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null
     */
    protected $tracker;

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
     * @var string|null
     */
    protected $groupHandle;

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

    public function add()
    {
        $this->set('title', '');
        $this->set('handle', '');
        $this->set('content', '');
        $this->set('openedByDefault', false);
        $this->set('showPermalink', true);
        $this->set('groupHandle', '');
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
        parent::save($normalized);
        $this->content = $normalized['content'];
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->track($this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::delete()
     */
    public function delete()
    {
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->forget($this);
        }
        parent::delete();
    }

    public function view()
    {
        $this->set('content', LinkAbstractor::translateFrom($this->content));
        $currentPage = Page::getCurrentPage();
        $this->set('currentPage', $currentPage && !$currentPage->isError() ? $currentPage : null);
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
        return static::getUsedFilesIn($this->content);
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

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        if (version_compare(APP_VERSION, '9.4.0') < 0) {
            $content = (string) $blockNode->data->record->content;
            if ($content !== '') {
                $contentFixed = LinkAbstractor::export($content);
                if ($contentFixed !== $content) {
                    unset($blockNode->data->record->content);
                    $xmlService = $this->app->make(Xml::class);
                    if (method_exists($xmlService, 'createChildElement')) {
                        $xmlService->createChildElement($blockNode->data->record, 'content', $contentFixed);
                    } else {
                        $xmlService->createCDataNode($blockNode->data->record, 'content', $contentFixed);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $args = parent::getImportData($blockNode, $page);
        if (version_compare(APP_VERSION, '9.2.1') < 0) {
            if (isset($blockNode->data->record->content)) {
                $args['content'] = LinkAbstractor::import((string) $blockNode->data->record->content);
            }
        }

        return $args;
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
        $normalized['groupHandle'] = isset($args['groupHandle']) && is_string($args['groupHandle']) ? trim($args['groupHandle']) : '';
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
     * @return \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected function getTracker()
    {
        if ($this->tracker === null) {
            $this->tracker = $this->app->make(AggregateTracker::class);
        }

        return $this->tracker;
    }

    /**
     * @param string|null $richText
     *
     * @return int[]|string[]
     */
    protected static function getUsedFilesIn($richText)
    {
        $richText = (string) $richText;
        if ($richText === '') {
            return [];
        }
        $rxIdentifier = '(?<id>[1-9][0-9]{0,18})';
        if (method_exists(\Concrete\Core\File\File::class, 'getByUUID')) {
            $rxIdentifier = '(?:(?<uuid>[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12})|' . $rxIdentifier . ')';
        }
        $result = [];
        $matches = null;
        foreach ([
            '/\<concrete-picture[^>]*?\bfID\s*=\s*[\'"]' . $rxIdentifier . '[\'"]/i',
            '/\bFID_DL_' . $rxIdentifier . '\b/',
        ] as $rx) {
            if (!preg_match_all($rx, $richText, $matches)) {
                continue;
            }
            $result = array_merge($result, array_map('intval', array_filter($matches['id'])));
            if (isset($matches['uuid'])) {
                $result = array_merge($result, array_map('strtolower', array_filter($matches['uuid'])));
            }
        }

        return $result;
    }
}

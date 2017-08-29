<?php
/**
 * Created by Pure/Web
 * www.pure-web.ru
 * © 2017
 */

namespace Concrete\Package\PureAccordion\Block\PureAccordion;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends BlockController implements FileTrackableInterface
{

    protected $btTable = "btPureAccordion";
    protected $btDefaultSet = 'other';
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "465";
    protected $btCacheBlockRecord = true;
    protected $btCacheBlockOutput = true;
    protected $btCacheBlockOutputOnPost = true;
    protected $btSupportsInlineEdit = false;
    protected $btSupportsInlineAdd = false;
    protected $btCacheBlockOutputLifetime = 0;

    public $title;
    public $content;
    public $openByDefault;
    /**
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected $tracker;


    public function getBlockTypeName()
    {
        return t('Accordion');
    }

    public function getBlockTypeDescription()
    {
        return t('A simple accordion.');
    }

    public function __construct($obj=null, AggregateTracker $tracker=null)
    {
        parent::__construct($obj);
        $this->tracker = $tracker;
    }

    public function validate($data)
    {

        $e = \Core::make('error');

        if (!$data['title']) {
            $e->add(t('%s is required', 'Title'));
        }

        if (strlen($data['title']) > 255) {
            $e->add(t('%s must be shorter than 255 characters', 'Title'));
        }

        if (!$data['content']) {
            $e->add(t('%s is required', 'Content'));
        }

        return $e;
    }

    public function getContent()
    {
        return LinkAbstractor::translateFrom($this->content);
    }

    public function getSearchableContent()
    {
        return $this->content;
    }

    public function getContentEditMode()
    {
        return LinkAbstractor::translateFromEditMode($this->content);
    }

    public function br2nl($str)
    {
        $str = str_replace("\r\n", "\n", $str);
        $str = str_replace("<br />\n", "\n", $str);

        return $str;
    }

    public function getUsedFiles()
    {
        $files = [];
        $matches = [];
        if (preg_match_all('/\<concrete-picture[^>]*?fID\s*=\s*[\'"]([^\'"]*?)[\'"]/i', $this->content, $matches)) {
            list(,$ids) = $matches;
            foreach ($ids as $id) {
                $files[] = intval($id);
            }
        }

        return $files;
    }

    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }

    public function add()
    {
        $this->set('showPermalink', 1);
    }

    public function view()
    {
        $this->requireAsset('css', 'animate');
        $this->set('content', $this->getContent());
    }

    public function save($data)
    {
        /** @var \Concrete\Core\Utility\Service\Text $th */
        $th = \Core::make('helper/text');
        $data['handle'] = $th->shortText($th->urlify($data['title']), 255, '');
        $data['handle'] = $data['handle'] .'-'. $this->bID;

        if (isset($data['content'])) {
            $data['content'] = LinkAbstractor::translateTo($data['content']);
        }
        $data['openedByDefault'] = intval($data['openedByDefault']);
        $data['showPermalink'] = intval($data['showPermalink']);

        parent::save($data);
        $this->tracker->track($this);
    }

    public function delete()
    {
        parent::delete();
        $this->tracker->forget($this);
    }

}

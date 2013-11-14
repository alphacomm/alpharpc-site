<?php
namespace AlphaRPC\Bundle\SiteBundle\Twig;

class Chapter
{
    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $label;

    /**
     *
     * @var int
     */
    protected $level;

    /**
     *
     * @var Chapter[]
     */
    protected $children;

    /**
     *
     * @var Chapter
     */
    protected $lastChild;

    /**
     *
     * @param string $id
     * @param string $label
     * @param int $level
     */
    public function __construct($id, $label, $level)
    {
        $this->id = $id;
        $this->label = $label;
        $this->level = $level;
        $this->children = array();
    }

    /**
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     *
     * @param Chapter $chapter
     * @return null
     * @throws InvalidArgumentException
     */
    public function insert(Chapter $chapter)
    {
        if ($chapter->level < $this->level) {
            throw new InvalidArgumentException('Only insert into root node.');
        }

        if ($chapter->level == $this->level+1) {
            $this->children[] = $this->lastChild = $chapter;
            return;
        }

        if ($this->lastChild === null) {
            $this->children[] = $this->lastChild = new Chapter(null, null, $this->level+1);
        }

        $this->lastChild->insert($chapter);
    }

    /**
     *
     * @return Chapter[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Creates an html string to display the sub chapters.
     *
     * @return string
     */
    public function toHTML()
    {
        $chapters = $this->children;
        if (count($chapters) == 0) {
            return '';
        }

        $html = '<ul class="nav">';
        foreach ($chapters as $chapter) {
            $html .= '<li>';
            if ($chapter->getId() !== null) {
                $html .= '<a href="#'.$chapter->getId().'">'.$chapter->getLabel().'</a>';
            }
            $html .= $chapter->toHTML();
            $html .= '</li>';
        }
        $html .= '</ul>';

        return $html;
    }
}
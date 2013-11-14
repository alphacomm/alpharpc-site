<?php
namespace AlphaRPC\Bundle\SiteBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class AlphaRPCExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('index', array($this, 'indexFilter'), array('is_safe' => array('html')))
        );
    }

    public function getName()
    {
        return 'alpharpc_extension';
    }

    public function indexFilter($content)
    {
        $matches = array();
        preg_match_all('#<h([1-6])([^>]*)>(.*)</h\1>#Us', $content, $matches, PREG_SET_ORDER);

        $chapter = new Chapter(null, null, 0);
        foreach ($matches as $match) {
            $level = $match[1];
            $id = preg_replace('#^.*\sid="([^"]+)".*$#', '\1', $match[2]);
            $label = $match[3];

            $chapter->insert(new Chapter($id, $label, $level));
        }
        return $chapter->toHTML();
    }
}


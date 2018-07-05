<?php
/**
 * Copyright (c) 2017-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\JSON;

use Facebook\InstantArticles\Elements\Element;
use Facebook\InstantArticles\Elements\Paragraph;
use Facebook\InstantArticles\Elements\Blockquote;
use Facebook\InstantArticles\Elements\Ad;
use Facebook\InstantArticles\Elements\Analytics;
use Facebook\InstantArticles\Elements\H1;
use Facebook\InstantArticles\Elements\H2;
use Facebook\InstantArticles\Elements\ListElement;
use Facebook\InstantArticles\Elements\Pullquote;
use Facebook\InstantArticles\Elements\Image;
use Facebook\InstantArticles\Elements\Caption;
use Facebook\InstantArticles\Elements\AnimatedGIF;
use Facebook\InstantArticles\Elements\Video;
use Facebook\InstantArticles\Elements\Audio;
use Facebook\InstantArticles\Elements\Slideshow;
use Facebook\InstantArticles\Elements\Interactive;
use Facebook\InstantArticles\Elements\SocialEmbed;
use Facebook\InstantArticles\Elements\Map;
use Facebook\InstantArticles\Elements\RelatedArticles;
use Facebook\InstantArticles\Elements\TextContainer;
use Facebook\InstantArticles\Elements\InstantArticleInterface;
use Facebook\InstantArticles\Elements\InstantArticle;

use Facebook\InstantArticles\Parser\Parser;
use Facebook\InstantArticles\Validators\Type;
use Facebook\InstantArticles\Utils\Observer;

class JSONArticle extends Element implements InstantArticleInterface
{
	const DEFAULT_WIDTH = 380;
    const DEFAULT_HEIGHT = 240;

	const DEFAULT_DATE_FORMAT = 'F d, Y';

	const MEDIA_SIZE_MODE_RESPONSIVE = 'responsive';
	const MEDIA_SIZE_MODE_VIEWPORT = 'viewport';
	const MEDIA_SIZE_MODE_SCALED = 'scaled';

    private $instantArticle;

    /**
     * @var Observer The instance for Observing and Hooking system for extensions
     */
    private $observer;

    private $dateFormat = JSONArticle::DEFAULT_DATE_FORMAT;
    private $logo;
    private $jsonHeader;

    private function __construct($instantArticle, $observer)
    {
        $this->instantArticle = $instantArticle;
        $this->observer = $observer;
    }

    /**
     * Factory method to instantiate the JSONArticle converter.
     * @param string|InstantArticle $instantArticle The instant article that will be parsed if informed as string.
     */
    public static function create($instantArticle)
    {
        // Treats if the informed content is string, parsing it into InstantArticle.
        if (Type::is($instantArticle, Type::STRING)) {
            libxml_use_internal_errors(true);
            $document = new \DOMDocument('1.0');
            $document->loadHTML($instantArticle);
            libxml_use_internal_errors(false);

            $parser = new Parser();
            $instantArticle = $parser->parse($document);
        }

        // Enforces that $instantArticle is typeof InstantArticle class.
        Type::enforce($instantArticle, InstantArticle::getClassName());

        return new self($instantArticle, Observer::create());
    }

    public function getObserver()
    {
        return $this->observer;
    }

    public function getInstantArticle()
    {
        return $this->instantArticle;
    }

    public function render($doctype = '', $format = true, $validate = true)
    {
		$doctype = '';
        $rendered = parent::render($doctype, $format, $validate);

        $simpleXML = simplexml_import_dom( $this->getInstantArticle()->toDOMElement() );
        return json_encode($simpleXML);
    }

	public function transformInstantArticle($context)
	{
		$html = $context->createElement('html');

		$article = $this->observer->applyFilters('JSON_ARTICLE', $this->transformArticleContent($context), $context);
        $context->withArticle($article);

		return $html;
	}

	public function transformArticleContent($context)
	{
		$article = $context->createElement('article', $context->getBody());

		if ($context->getInstantArticle()->getChildren()) {
			foreach ($context->getInstantArticle()->getChildren() as $child) {
				$context->addItem($child);
			}
		}

		return $article;
	}

    public function toDOMElement($document = null)
    {
		$context = JSONContext::create($document, $this->getInstantArticle());
		$jsonDocument = $this->observer->applyFilters('JSON_DOCUMENT', $this->transformInstantArticle($context), $context);

        return $jsonDocument;
    }
}

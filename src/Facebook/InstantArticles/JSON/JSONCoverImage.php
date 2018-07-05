<?php
/**
 * Copyright (c) 2017-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\JSON;

class JSONCoverImage
{
    // Constructor setup
    private $image;
    private $context;

    // Generator fill in
    private $containerTag;
    private $jsonImgTag;

    private function __construct($image, $context)
    {
        $this->image = $image;
        $this->context = $context;
    }

    public static function create($image, $context)
    {
        return new self($image, $context);
    }

    private function genContainer()
    {
        $this->containerTag = $this->context->createElement('div', null);
    }

    private function genCaptionContainer()
    {
        $caption = $this->image->getCaption();
        if ($caption) {
            $this->ampImgTag =
                JSONCaption::create($caption, $this->context, $this->ampImgTag)->build();
        }
    }

    private function genAmpImage()
    {
        $this->ampImgTag = $this->context->createElement('img', null);
        $imageURL = $this->image->getUrl();

        $imageDimensions = $this->context->getMediaDimensions($imageURL, JSONContext::MEDIA_TYPE_IMAGE);
        $imageWidth = $imageDimensions[0];
        $imageHeight = $imageDimensions[1];

        $horizontalScale = $imageWidth > 0 ? JSONContext::DEFAULT_WIDTH / $imageWidth : 0;
        $verticalScale = $imageHeight > 0 ? JSONContext::DEFAULT_HEIGHT / $imageHeight : 0;
        $maxScale = ($horizontalScale > 0 || $verticalScale > 0) ? max($horizontalScale, $verticalScale) : 0;

        $translateX = (int) (-($imageWidth * $maxScale - JSONContext::DEFAULT_WIDTH) / 2);
        $translateY = (int) (-($imageHeight * $maxScale - JSONContext::DEFAULT_HEIGHT) / 2);

        $imageWidth = (int) ($imageWidth * $maxScale);
        $imageHeight = (int) ($imageHeight * $maxScale);

        $this->ampImgTag->setAttribute('src', $imageURL);
        $this->ampImgTag->setAttribute('width', (string) $imageWidth);
        $this->ampImgTag->setAttribute('height', (string) $imageHeight);
        $this->ampImgTag->setAttribute('layout', 'responsive');
    }

    public function build()
    {
        $this->genContainer();
        $this->genAmpImage();
        $this->genCaptionContainer();

        $this->containerTag->appendChild($this->ampImgTag);
        return $this->containerTag;
    }
}

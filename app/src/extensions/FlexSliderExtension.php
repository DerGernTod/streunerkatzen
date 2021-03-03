<?php
namespace Streunerkatzen\Extensions;

use Dynamic\FlexSlider\ORM\FlexSlider;
use SilverStripe\View\Requirements;

class FlexSliderExtension extends FlexSlider {
    public function getCustomScript() {
        $showThumbnailNav = $this->owner->ThumbnailNav == true && $this->owner->Slides()->Count() > 1;

        // Flexslider options
        $sync = $showThumbnailNav ? "sync: '#fs-carousel-".$this->owner->ID."'," : '';

        $before = $this->owner->hasMethod('flexSliderBeforeAction')
            ? $this->owner->flexSliderBeforeAction()
            : 'function(){}';

        $after = $this->owner->hasMethod('flexSliderAfterAction')
            ? $this->owner->flexSliderAfterAction()
            : 'function(){}';

        $speed = $this->getSlideshowSpeed();

        $customScript = "";

        if ($showThumbnailNav) {
            $customScript .= "jQuery('#fs-carousel-".$this->owner->ID."').flexslider({
                slideshow: " . $this->owner->obj('Animate')->NiceAsBoolean() . ",
                animation: 'slide',
                animationLoop: " . $this->owner->obj('Loop')->NiceAsBoolean() . ",
                controlNav: " . $this->owner->obj('CarouselControlNav')->NiceAsBoolean() . ",
                directionNav: " . $this->owner->obj('CarouselDirectionNav')->NiceAsBoolean() . ",
                prevText: '',
                nextText: '',
                pausePlay: false,
                asNavFor: '#flexslider-".$this->owner->ID."',
                minItems: " . $this->owner->obj('CarouselThumbnailCt') . ",
                maxItems: " . $this->owner->obj('CarouselThumbnailCt') . ",
                move: " . $this->owner->obj('CarouselThumbnailCt') . ",
                itemWidth: 100,
                itemMargin: 10
            });";
        }

        $customScript .= "(function($) {
                $(document).ready(function(){
                    jQuery('#flexslider-".$this->owner->ID."').flexslider({
                        slideshow: " . $this->owner->obj('Animate')->NiceAsBoolean() . ",
                        animation: '" . $this->owner->Animation . "',
                        animationLoop: " . $this->owner->obj('Loop')->NiceAsBoolean() . ",
                        controlNav: " . $this->owner->obj('SliderControlNav')->NiceAsBoolean() . ",
                        directionNav: " . $this->owner->obj('SliderDirectionNav')->NiceAsBoolean() . ",
                        prevText: '',
                        nextText: '',
                        pauseOnAction: true,
                        pauseOnHover: true,
                        " . $sync . "
                        start: function(slider){
                            $('body').removeClass('loading');
                            window.dispatchEvent(new Event('sliderready'));
                        },
                        before: " . $before . ",
                        after: " . $after . ",
                        slideshowSpeed: " . $speed . "
                });";

        $customScript .= "});
            }(jQuery));";

        Requirements::customScript($customScript);
    }
}

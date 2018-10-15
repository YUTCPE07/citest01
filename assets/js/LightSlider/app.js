 $(document).ready(function() {
    var slider = $("#lightSlider").lightSlider();
    slider.goToSlide(3);
    slider.goToPrevSlide();
    slider.goToNextSlide();
    slider.getCurrentSlideCount();
    slider.refresh();
    slider.play(); 
    slider.pause();    
  });
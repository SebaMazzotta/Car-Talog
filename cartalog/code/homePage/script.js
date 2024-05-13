let slide = document.querySelectorAll('.slide');
var current = 0;
var slideInterval;

function cls() {
  for (let i = 0; i < slide.length; i++) {
    slide[i].style.display = 'none';
  }
}

function next() {
  cls();
  if (current === slide.length - 1) current = -1;
  current++;

  slide[current].style.display = 'block';
  slide[current].style.opacity = 0.4;

  var x = 0.4;
  var intX = setInterval(function() {
    x += 0.1;
    slide[current].style.opacity = x;
    if (x >= 1) {
      clearInterval(intX);
      x = 0.4;
    }
  }, 100);

  updateDots();
}

function prev() {
  cls();
  if (current === 0) current = slide.length;
  current--;

  slide[current].style.display = 'block';
  slide[current].style.opacity = 0.4;

  var x = 0.4;
  var intX = setInterval(function() {
    x += 0.1;
    slide[current].style.opacity = x;
    if (x >= 1) {
      clearInterval(intX);
      x = 0.4;
    }
  }, 100);

  updateDots();
}

function updateDots() {
  const dots = document.querySelectorAll('.dot');
  dots.forEach(dot => {
    dot.classList.remove('active');
  });
  dots[current].classList.add('active');
}

function start(){
    cls();
    slide[current].style.display = 'block';
    updateDots();
    startSlideShow();
}


function startSlideShow() {
  slideInterval = setInterval(next, 5000);
}

function currentSlide(n) {
  cls();
  current = n - 1;
  slide[current].style.display = 'block';
  updateDots();
}

function updateDots() {
  const dots = document.querySelectorAll('.dot');
  dots.forEach(dot => {
    dot.classList.remove('active');
  });
  dots[current].classList.add('active');
}

start();
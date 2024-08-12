    <!-- Hero Start -->
<div class="container-fluid pt-5 bg-primary hero-header mb-5 position-relative">
    <div id="particles-js" class="position-absolute w-100 h-100" style="top: 0; left: 0;"></div>
    <div class="container pt-5 position-relative" style="z-index: 1;">
        <div class="row g-5 pt-5">
            <div class="col-lg-6 align-self-center text-center text-lg-start mb-lg-5">
                <div class="btn btn-sm border rounded-pill text-white px-3 mb-3 animated slideInRight">AI.Tech</div>
                <h6 class="display-4 text-white mb-4 animated slideInRight">Outsource from Our Pool of Talented Young Professionals</h6>
                <p class="text-white mb-4 animated slideInRight">Tap into our diverse talent pool of skilled experts across various fields. Whether you need creative, technical, or administrative support, our qualified professionals are ready to help you do more. Streamline your projects and boost productivity by partnering with us today!</p>
                <a href="" class="btn btn-light py-sm-3 px-sm-5 rounded-pill me-3 animated slideInRight">Post A Job</a>
                <a href="" class="btn btn-outline-light py-sm-3 px-sm-5 rounded-pill animated slideInRight">Become A Freelancer</a>
            </div>
            <div class="col-lg-6 align-self-end text-center text-lg-end">
              <img class="img-fluid" src="img/lizbg.png" alt="" style="width: 100%; height: auto;">
            </div>

        </div>
    </div>
</div>
<!-- Hero End -->

<style>
    .hero-header {
    position: relative;
    overflow: hidden;
}

#particles-js {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

@media (max-width: 767.98px) {
    .hero-header img {
        display: none;
    }
}


</style>


     <script>
    document.addEventListener('DOMContentLoaded', (event) => {
      particlesJS('particles-js', {
        particles: {
          number: {
            value: 80,
            density: {
              enable: true,
              value_area: 800
            }
          },
          color: {
            value: "#ffffff"
          },
          shape: {
            type: "circle",
            stroke: {
              width: 0,
              color: "#000000"
            },
            polygon: {
              nb_sides: 5
            },
            image: {
              src: "img/github.svg",
              width: 100,
              height: 100
            }
          },
          opacity: {
            value: 0.5,
            random: false,
            anim: {
              enable: false,
              speed: 1,
              opacity_min: 0.1,
              sync: false
            }
          },
          size: {
            value: 3,
            random: true,
            anim: {
              enable: false,
              speed: 40,
              size_min: 0.1,
              sync: false
            }
          },
          line_linked: {
            enable: true,
            distance: 150,
            color: "#ffffff",
            opacity: 0.4,
            width: 1
          },
          move: {
            enable: true,
            speed: 6,
            direction: "none",
            random: false,
            straight: false,
            out_mode: "out",
            bounce: false,
            attract: {
              enable: false,
              rotateX: 600,
              rotateY: 1200
            }
          }
        },
        interactivity: {
          detect_on: "canvas",
          events: {
            onhover: {
              enable: true,
              mode: "repulse"
            },
            onclick: {
              enable: true,
              mode: "push"
            },
            resize: true
          },
          modes: {
            grab: {
              distance: 400,
              line_linked: {
                opacity: 1
              }
            },
            bubble: {
              distance: 400,
              size: 40,
              duration: 2,
              opacity: 8,
              speed: 3
            },
            repulse: {
              distance: 200,
              duration: 0.4
            },
            push: {
              particles_nb: 4
            },
            remove: {
              particles_nb: 2
            }
          }
        },
        retina_detect: true
      });
    });
  </script>

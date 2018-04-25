<style>
    #nprogress .bar{top:0!important;}
    #nprogress .spinner{top:26px!important;}
    .logo {font-size: 42px;}
    .step1-container{margin:auto;padding:15px 30%;color:#444}
    .account-pages{min-height:800px}
</style>
<div id="account-pages" class="account-pages"></div>
<div class="text-center" style="margin-top:40px">
    <a class="logo"><span>LXH</span></a>
</div>
<div class="step1-container"><?php echo $card->render()?></div>
<script>
    $('#account-pages').css('height', $(window).height());
    require_js(['@lxh/js/particles.min']);
    __then__(function () {
        particlesJS("account-pages", {
            particles: {
                number: {value: 40, density: {enable: !0, value_area: 388}},
                color: {value: "#399c9c"},
                shape: {type: "circle", stroke: {width: 0, color: "#000000"}, polygon: {nb_sides: 5}},
                opacity: {value: .3, random: !1, anim: {enable: !1, speed: 1, opacity_min: .2, sync: !1}},
                size: {value: 20, random: !0, anim: {enable: !1, speed: 50, size_min: .1, sync: !1}},
                line_linked: {enable:0, distance: 250, color: "#40afaf", opacity: .3, width: 1},
                move: {
                    enable: !0,
                    speed: 3,
                    direction: "none",
                    random: !0,
                    straight: !1,
                    out_mode: "out",
                    bounce: !0,
                    attract: {enable: !1, rotateX: 600, rotateY: 1200}
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {onhover: {enable: !0, mode: "grab"}, onclick: {enable: !0, mode: "push"}, resize: !0},
                modes: {grab: {distance: 140, line_linked: {opacity: 1}}}
            },
            retina_detect: !0
        });
    });

</script>

<section class="hero position-relative text-center" style="background: #ffffff; overflow: hidden; min-height: 90vh; display: flex; align-items: center; justify-content: center;">
    <!-- Particle Canvas Background -->
    <canvas id="hero-particles" class="position-absolute w-100 h-100" style="top: 0; left: 0; z-index: 0; pointer-events: none;"></canvas>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="hero-content d-flex flex-column align-items-center">
                    
                    <span class="hero-tag mb-4 fade-up-badge">
                         Global Supply Chain Platform
                    </span>
                    
                    <!-- Fixed height prevents layout shift during typing -->
                    <h1 class="hero-title mb-4" style="color: #1F2937; font-size: 64px; font-weight: 800; line-height: 1.2; letter-spacing: -1px; min-height: 154px; display: flex; justify-content: center; align-items: center; flex-wrap: wrap;">
                        <span>
                            <span id="typewriter-text"></span><span class="cursor">|</span>
                        </span>
                    </h1>
                    
                    <div id="hero-post-typing" style="opacity: 0; transform: translateY(20px);">
                        <p class="mb-5 mx-auto" style="color: #6B7280; font-size: 20px; line-height: 1.8; max-width: 700px;">
                            Monitor weather, logistics, exchange rates, <br>
                            economy, ports, and geopolitical risks <br>
                            from one intelligent dashboard.
                        </p>
                        
                        <div class="hero-buttons d-flex justify-content-center gap-4">
                            <a href="#" class="btn btn-primary-custom btn-hover-anim px-5 py-3 rounded-pill" style="font-size: 16px;">Explore Dashboard</a>
                            <a href="#" class="btn btn-outline-custom btn-hover-anim px-5 py-3 rounded-pill" style="font-size: 16px;">Learn More</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Reset hero default to fix layout */
.hero { min-height: auto; }

/* Badge Animation */
.fade-up-badge {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeUp 1s ease-out forwards;
}

@keyframes fadeUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Typewriter Cursor */
.cursor {
    display: inline-block;
    color: #0F766E;
    font-weight: 300;
    margin-left: 2px;
    animation: blink 1s step-end infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0; }
}

/* Post Typing Reveal */
.show-post-typing {
    transition: opacity 1s cubic-bezier(0.16, 1, 0.3, 1), transform 1s cubic-bezier(0.16, 1, 0.3, 1);
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Custom Buttons */
.btn-primary-custom {
    background: linear-gradient(135deg, #0F766E, #065F46);
    color: white;
    border: none;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-primary-custom:hover {
    color: white;
}

.btn-outline-custom {
    background: transparent;
    color: #1F2937;
    border: 2px solid #E5E7EB;
    text-decoration: none;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: white;
}

.btn-outline-custom:hover {
    border-color: #0F766E;
    color: #0F766E;
    background: #F0FDF4;
}

/* Button Hover Animation */
.btn-hover-anim {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1) !important;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.btn-hover-anim::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
    z-index: -1;
}

.btn-outline-custom.btn-hover-anim::before {
    background: rgba(15, 118, 110, 0.05);
}

.btn-hover-anim:hover::before {
    width: 400px;
    height: 400px;
}

.btn-primary-custom.btn-hover-anim:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(15, 118, 110, 0.25);
}

.btn-outline-custom.btn-hover-anim:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .hero-title { font-size: 48px !important; min-height: 120px !important; }
}
@media (max-width: 768px) {
    .hero-title { font-size: 36px !important; min-height: 100px !important; }
    .hero-buttons { flex-direction: column; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- Typewriter Effect ---
    const textToType = "Global Supply Chain Risk Intelligence Platform";
    const speed = 40; // milliseconds per character
    let i = 0;
    const typeWriterElem = document.getElementById("typewriter-text");
    const postTypingElem = document.getElementById("hero-post-typing");
    let isTyping = false;

    function typeWriter() {
        if (i < textToType.length) {
            typeWriterElem.innerHTML += textToType.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        } else {
            // Finished typing
            isTyping = false;
            // Fade in description and buttons
            setTimeout(() => {
                postTypingElem.classList.add('show-post-typing');
            }, 200);
        }
    }
    
    // Start typing slightly after page load
    setTimeout(() => {
        isTyping = true;
        typeWriter();
    }, 600);


    // --- Particle Animation ---
    const canvas = document.getElementById("hero-particles");
    if (!canvas) return;
    
    const ctx = canvas.getContext("2d");
    let width, height;
    let particles = [];
    
    // Mouse interaction
    let mouse = { x: null, y: null, radius: 180 };
    
    window.addEventListener('mousemove', function(e) {
        const rect = canvas.getBoundingClientRect();
        mouse.x = e.clientX - rect.left;
        mouse.y = e.clientY - rect.top;
    });
    
    window.addEventListener('mouseout', function() {
        mouse.x = null;
        mouse.y = null;
    });

    function resize() {
        width = canvas.width = canvas.offsetWidth;
        height = canvas.height = canvas.offsetHeight;
    }
    
    const heroSection = document.querySelector('.hero');
    if (window.ResizeObserver) {
        const ro = new ResizeObserver(() => resize());
        ro.observe(heroSection);
    } else {
        window.addEventListener("resize", resize);
    }
    
    resize();

    class Particle {
        constructor() {
            this.x = Math.random() * width;
            this.y = Math.random() * height;
            // Slow, smooth movement
            this.vx = (Math.random() - 0.5) * 0.3;
            this.vy = (Math.random() - 0.5) * 0.3;
            this.size = Math.random() * 1.5 + 0.5;
            this.color = "rgba(15, 118, 110, 0.3)"; // Soft green
        }
        update() {
            this.x += this.vx;
            this.y += this.vy;
            
            // Bounce off edges
            if(this.x < 0 || this.x > width) this.vx = -this.vx;
            if(this.y < 0 || this.y > height) this.vy = -this.vy;

            // Mouse repulsion
            if (mouse.x != null && mouse.y != null) {
                let dx = mouse.x - this.x;
                let dy = mouse.y - this.y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                if (distance < mouse.radius) {
                    const forceDirectionX = dx / distance;
                    const forceDirectionY = dy / distance;
                    const force = (mouse.radius - distance) / mouse.radius;
                    this.x -= forceDirectionX * force * 1.5;
                    this.y -= forceDirectionY * force * 1.5;
                }
            }
        }
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fillStyle = this.color;
            ctx.fill();
        }
    }

    function init() {
        particles = [];
        // Determine number of particles based on screen area
        let numParticles = Math.min(150, Math.floor((width * height) / 9000));
        
        for(let i=0; i<numParticles; i++) {
            particles.push(new Particle());
        }
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        for(let i=0; i<particles.length; i++) {
            particles[i].update();
            particles[i].draw();
            
            for(let j = i + 1; j < particles.length; j++) {
                let dx = particles[i].x - particles[j].x;
                let dy = particles[i].y - particles[j].y;
                let distance = Math.sqrt(dx * dx + dy * dy);
                
                if(distance < 150) {
                    ctx.beginPath();
                    // Connect lines, fading out based on distance
                    ctx.strokeStyle = `rgba(15, 118, 110, ${0.12 - distance/1250})`;
                    ctx.lineWidth = 0.8;
                    ctx.moveTo(particles[i].x, particles[i].y);
                    ctx.lineTo(particles[j].x, particles[j].y);
                    ctx.stroke();
                }
            }
        }
        requestAnimationFrame(animate);
    }
    
    init();
    animate();
});
</script>
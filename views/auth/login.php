<?php
// ==========================================
// 1. SERVER-SIDE CHECK
// ==========================================
// This code MUST be at the very top (Line 1).
// It checks if you are logged in. If yes, it sends you to Home immediately.

// Adjust this path if your config folder is in a different location
$authConfigPath = __DIR__ . '/../../../config/auth.php';

if (file_exists($authConfigPath)) {
    require_once $authConfigPath;
    
    // If the server says "User is logged in", go to home immediately.
    if (function_exists('isLoggedIn') && isLoggedIn()) {
        header("Location: index.php?page=home");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaghalny - Login</title>
    
    <!-- React (CDNJS) & Pure JS Three.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Relative Asset Paths -->
    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?php echo time(); ?>">
    
    <!-- Components (Pure JS) -->
    <script src="assets/js/components/LoginForm.js?v=<?php echo time(); ?>"></script>
    <script src="assets/js/admin_shortcuts.js?v=<?php echo time(); ?>"></script>
</head>
<body>
    
    <div id="root"></div>
    
    <!-- Pure JS Auth Script -->
    <script>
        (function() {
            const { useState, useEffect, useRef, createElement: h } = React;

            // --- 1. VISUALS (Three.js) ---
            const LeftVisual = () => {
                const mountRef = useRef(null);
                useEffect(() => {
                    if (!mountRef.current) return;
                    let animationFrameId;
                    let renderer;

                    const initThree = () => {
                        // (Same Three.js Logic - Collapsed for brevity as logic is identical)
                        const width = mountRef.current.clientWidth || window.innerWidth / 2;
                        const height = mountRef.current.clientHeight || window.innerHeight;
                        if (width === 0 || height === 0) { setTimeout(initThree, 100); return; }

                        const scene = new THREE.Scene();
                        scene.background = new THREE.Color(0x15223C); 
                        scene.fog = new THREE.FogExp2(0x15223C, 0.002);
                        const camera = new THREE.PerspectiveCamera(75, width / height, 1, 2000);
                        camera.position.z = 600; camera.position.y = 200;
                        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
                        renderer.setSize(width, height);
                        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                        mountRef.current.innerHTML = ''; 
                        mountRef.current.appendChild(renderer.domElement);

                        // Particles Geometry
                        const geometry = new THREE.BufferGeometry();
                        const positions = [], colors = [];
                        const c1 = new THREE.Color(0xecf2f9), c2 = new THREE.Color(0xa0aec0);
                        const sep = 40, numX = 80, numZ = 80;
                        for (let ix = 0; ix < numX; ix++) {
                            for (let iz = 0; iz < numZ; iz++) {
                                positions.push(ix * sep - ((numX * sep) / 2), 0, iz * sep - ((numZ * sep) / 2));
                                const mixed = c1.clone().lerp(c2, ix / numX);
                                colors.push(mixed.r, mixed.g, mixed.b);
                            }
                        }
                        geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
                        geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
                        const material = new THREE.PointsMaterial({ size: 8, vertexColors: true, alphaTest: 0.5, transparent: true, opacity: 0.9, sizeAttenuation: true });
                        new THREE.TextureLoader().load('https://threejs.org/examples/textures/sprites/disc.png', (tex) => {
                            material.map = tex;
                            material.needsUpdate = true; 
                        });
                        const particles = new THREE.Points(geometry, material);
                        scene.add(particles);

                        let mouseX = 0, mouseY = 0, count = 0;
                        window.addEventListener('mousemove', (e) => { mouseX = e.clientX - width / 2; mouseY = e.clientY - height / 2; });

                        const animate = () => {
                            animationFrameId = requestAnimationFrame(animate);
                            count += 0.05;
                            const pos = particles.geometry.attributes.position.array;
                            let i = 0;
                            for (let ix = 0; ix < numX; ix++) {
                                for (let iz = 0; iz < numZ; iz++) {
                                    pos[i + 1] = (Math.sin((ix + count) * 0.3) * 30) + (Math.sin((iz + count) * 0.5) * 30);
                                    i += 3;
                                }
                            }
                            particles.geometry.attributes.position.needsUpdate = true;
                            camera.position.x += (mouseX - camera.position.x) * 0.05;
                            camera.position.y += (-mouseY + 200 - camera.position.y) * 0.05;
                            camera.lookAt(scene.position);
                            renderer.render(scene, camera);
                        };
                        animate();
                    };
                    const cleanupListeners = initThree();
                    return () => { if(renderer) renderer.dispose(); cancelAnimationFrame(animationFrameId); };
                }, []);

                return h("div", { className: "auth-visual" }, [
                    h("div", { key: "canvas", ref: mountRef, style: { position: "fixed", top:0, left:0, width: "50%", height: "100%" } }),
                    h("div", { key: "logo", className: "auth-logo-block" }, [
                        h("div", { key: "m", className: "logo-main" }, [
                            h("span", { key: "1", className: "text-sha" }, "Sha"),
                            h("span", { key: "2", className: "char-g-container" }, "غ"),
                            h("span", { key: "3", className: "text-alny" }, "alny")
                        ]),
                        h("div", { key: "sub", className: "logo-below" }, "Empowering teens to work.")
                    ])
                ]);
            };

            // --- 2. LOGIN PAGE WRAPPER ---
            const LoginPage = ({ onFlip }) => {
                const [email, setEmail] = useState("");
                const [password, setPassword] = useState("");
                const [showPassword, setShowPassword] = useState(false);
                const [shake, setShake] = useState(false);
                const [error, setError] = useState("");
                const [isLoading, setIsLoading] = useState(false);

                // Handle Logic (Same as before)
                useEffect(() => { if (error) setTimeout(() => setError(""), 3000); }, [error]);

                const handleSubmit = async (e) => {
                    e.preventDefault();
                    if(!email || !password) { setError("Fill all fields"); setShake(true); setTimeout(()=>setShake(false),400); return; }
                    setIsLoading(true);
                    try {
                        const fd = new FormData();
                        fd.append('email', email); fd.append('password', password); fd.append('action', 'login');
                        const res = await fetch('index.php?controller=auth', { method: 'POST', body: fd });
                        const data = await res.json();
                        if (data.success) window.location.href = data.redirect || 'index.php?page=home';
                        else { setError(data.message || "Invalid credentials"); setShake(true); setTimeout(()=>setShake(false),400); }
                    } catch(e) { setError("Connection Error"); setShake(true); setTimeout(()=>setShake(false),400); }
                    finally { setIsLoading(false); }
                };

                return h(window.LoginForm, {
                    email, setEmail, password, setPassword, showPassword, setShowPassword,
                    handleFlipToRegister: (e) => { e.preventDefault(); onFlip(); },
                    handleSubmit, error, shake
                });
            };

            // --- 3. REGISTER FORM ---
            const RegisterForm = ({ onFlip }) => {
                const [form, setForm] = useState({ name: "", email: "", password: "", confirmPassword: "" });
                const [isLoading, setIsLoading] = useState(false);
                const [error, setError] = useState("");
                const [shake, setShake] = useState(false);
                const handleChange = (e) => setForm({...form, [e.target.name]: e.target.value});

                const handleSubmit = async (e) => {
                    e.preventDefault();
                    if(!form.name.includes(" ")) { setError("Full Name Required"); setShake(true); setTimeout(()=>setShake(false),400); return; }
                    
                    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.(com|org)$/;
                    if (!emailRegex.test(form.email)) { 
                        setError("Email must be a valid .com or .org address"); 
                        setShake(true); 
                        setTimeout(()=>setShake(false),400); 
                        return; 
                    }

                    if(form.password !== form.confirmPassword) { setError("Passwords do not match"); setShake(true); setTimeout(()=>setShake(false),400); return; }
                    setIsLoading(true);
                    try {
                        const fd = new FormData();
                        fd.append('name', form.name); fd.append('email', form.email); fd.append('password', form.password); fd.append('action', 'register');
                        const res = await fetch('index.php?controller=auth', { method: 'POST', body: fd });
                        const data = await res.json();
                        if(data.success) window.location.href = data.redirect || 'index.php?page=home';
                        else { setError(data.message); setShake(true); setTimeout(()=>setShake(false),400); }
                    } catch(e) { setError("Connection Error"); } finally { setIsLoading(false); }
                };

                return h("div", { className: `new-form-container ${shake ? 'shake' : ''}` }, 
                    h("form", { onSubmit: handleSubmit, className: "fade-in" }, [
                        h("div", { key: "h", className: "mb-6" }, [
                            h("h2", { className: "text-xl font-bold mb-1" }, "Join Shaghalny!"),
                            h("p", { className: "text-gray-500 text-xs" }, "Create your account to get started")
                        ]),
                        error && h("div", { className: "auth-error" }, h("p", { className: "auth-error-text" }, error)),
                        
                        // Inputs
                        [
                            {l:"Name", n:"name", t:"text", p:"Full Name"},
                            {l:"Email", n:"email", t:"email", p:"Email Address"},
                            {l:"Password", n:"password", t:"password", p:"Password"},
                            {l:"Confirm", n:"confirmPassword", t:"password", p:"Confirm Password"}
                        ].map(f => h("div", { key: f.n, className: "mb-3" }, [
                            h("label", { className: "form-label" }, f.l),
                            h("input", { type: f.t, name: f.n, value: form[f.n], onChange: handleChange, placeholder: f.p, className: "form-input" })
                        ])),

                        h("button", { type: "submit", className: "auth-submit-btn", disabled: isLoading }, isLoading ? "..." : "Sign Up"),
                        h("div", { className: "auth-link" }, [
                            "Already have an account? ",
                            h("span", { onClick: onFlip, style: {cursor:'pointer', color:'#3b82f6', fontWeight:'600'} }, "Sign In")
                        ])
                    ])
                );
            };

            // --- 4. MAIN APP ---
            function AuthPage() {
                const [isFlipped, setIsFlipped] = useState(false);
                useEffect(() => {
                    const params = new URLSearchParams(window.location.search);
                    if (params.get('page') === 'register') setIsFlipped(true);
                }, []);
                const toggleFlip = () => {
                    setIsFlipped(!isFlipped);
                    window.history.pushState({}, '', `?page=${!isFlipped ? 'register' : 'login'}`);
                };

                // Updated to use auth.css classes correctly: Section > Wrapper > Flip
                return h("div", { className: "auth-page" }, [
                    // Left Visual
                    h("div", { key: "vis", className: "auth-visual" }, h(LeftVisual)),
                    
                    // Right Form Section (Centers Content)
                    h("div", { key: "form-sect", className: "auth-form-section" }, 
                        h("div", { className: "auth-form-wrapper" }, [
                            h("div", { key: "b1", className: "auth-blob-1" }),
                            h("div", { key: "b2", className: "auth-blob-2" }),
                            h("div", { key: "flip", className: "flip-container" }, 
                                h("div", { className: `flip-inner ${isFlipped ? 'flipped' : ''}` }, [
                                    h("div", { key: "fr", className: "flip-front" }, h(LoginPage, { onFlip: toggleFlip })),
                                    h("div", { key: "bk", className: "flip-back" }, h(RegisterForm, { onFlip: toggleFlip }))
                                ])
                            )
                        ])
                    )
                ]);
            }

            const root = ReactDOM.createRoot(document.getElementById('root'));
            root.render(h(AuthPage));
        })();
    </script>
</body>
</html>

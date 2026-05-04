<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shaghalny - Reset Password</title>
    
    <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
    <script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
    <script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r134/three.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="/shaghalny/public/assets/css/auth.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="root"></div>
    <script type="text/babel">
        const { useState, useEffect, useRef } = React;

        // ==============================================
        // 1. LEFT VISUAL (Identical to Login)
        // ==============================================
        const LeftVisual = () => {
            const mountRef = useRef(null);
            
            useEffect(() => {
                if (!mountRef.current) return;
                
                let animationFrameId;
                let renderer;
                
                const initThree = () => {
                    const width = mountRef.current.clientWidth || window.innerWidth / 2;
                    const height = mountRef.current.clientHeight || window.innerHeight;
                    
                    if (width === 0 || height === 0) {
                        setTimeout(initThree, 100);
                        return;
                    }
                    
                    const scene = new THREE.Scene();
                    scene.background = new THREE.Color(0x15223C); 
                    scene.fog = new THREE.FogExp2(0x15223C, 0.002);
                    
                    const camera = new THREE.PerspectiveCamera(75, width / height, 1, 2000);
                    camera.position.z = 600; 
                    camera.position.y = 200; 
                    
                    renderer = new THREE.WebGLRenderer({ antialias: true, alpha: false });
                    renderer.setSize(width, height);
                    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
                    mountRef.current.innerHTML = ''; 
                    mountRef.current.appendChild(renderer.domElement);

                    // --- PARTICLES ---
                    const geometry = new THREE.BufferGeometry();
                    const positions = [];
                    const colors = [];
                    
                    const c1 = new THREE.Color(0x2D6BE0); 
                    const c2 = new THREE.Color(0x34A853); 
                    
                    const sep = 40; 
                    const numX = 80;
                    const numZ = 80;
                    for (let ix = 0; ix < numX; ix++) {
                        for (let iz = 0; iz < numZ; iz++) {
                            const x = ix * sep - ((numX * sep) / 2);
                            const z = iz * sep - ((numZ * sep) / 2);
                            const y = 0;
                            positions.push(x, y, z);
                            const ratio = ix / numX;
                            const mixed = c1.clone().lerp(c2, ratio);
                            colors.push(mixed.r, mixed.g, mixed.b);
                        }
                    }
                    geometry.setAttribute('position', new THREE.Float32BufferAttribute(positions, 3));
                    geometry.setAttribute('color', new THREE.Float32BufferAttribute(colors, 3));
                    
                    const material = new THREE.PointsMaterial({ 
                        size: 8, 
                        vertexColors: true, 
                        alphaTest: 0.5, 
                        transparent: true, 
                        opacity: 0.9,
                        sizeAttenuation: true
                    });
                    
                    new THREE.TextureLoader().load(
                        'https://threejs.org/examples/textures/sprites/disc.png',
                        (texture) => {
                            material.map = texture;
                            material.needsUpdate = true;
                        }
                    );
                    
                    const particles = new THREE.Points(geometry, material);
                    scene.add(particles);

                    // --- SHAPES ---
                    const shapeGroup = new THREE.Group();
                    const shapeGeo = new THREE.IcosahedronGeometry(15, 0);
                    const shapeMat = new THREE.MeshBasicMaterial({ 
                        color: 0x2D6BE0, 
                        wireframe: true, 
                        transparent: true, 
                        opacity: 0.15 
                    });
                    for (let i = 0; i < 15; i++) {
                        const mesh = new THREE.Mesh(shapeGeo, shapeMat);
                        mesh.position.set(
                            (Math.random() - 0.5) * 800,
                            (Math.random() - 0.5) * 400 + 150,
                            (Math.random() - 0.5) * 600
                        );
                        mesh.scale.setScalar(Math.random() * 2 + 0.5);
                        mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, 0);
                        shapeGroup.add(mesh);
                    }
                    scene.add(shapeGroup);

                    // --- ANIMATION ---
                    let mouseX = 0, mouseY = 0;
                    const handleMove = (e) => { 
                        mouseX = e.clientX - width / 2; 
                        mouseY = e.clientY - height / 2; 
                    };
                    window.addEventListener('mousemove', handleMove);

                    let count = 0;
                    const animate = () => {
                        animationFrameId = requestAnimationFrame(animate);
                        count += 0.05; 
                        
                        shapeGroup.rotation.y += 0.001;
                        shapeGroup.children.forEach(mesh => {
                            mesh.rotation.x += 0.005;
                            mesh.rotation.y += 0.005;
                        });

                        const positions = particles.geometry.attributes.position.array;
                        let i = 0;
                        for (let ix = 0; ix < numX; ix++) {
                            for (let iz = 0; iz < numZ; iz++) {
                                const x = positions[i];
                                const z = positions[i+2];
                                const dx = x - (mouseX * 0.5); 
                                const dz = z - (-mouseY * 0.5 + 200);
                                let y = (Math.sin((ix + count) * 0.3) * 30) + 
                                        (Math.sin((iz + count) * 0.5) * 30);
                                positions[i + 1] = y;
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
                    
                    const handleResize = () => {
                        if (!mountRef.current) return;
                        const w = mountRef.current.clientWidth || window.innerWidth / 2;
                        const h = mountRef.current.clientHeight || window.innerHeight;
                        renderer.setSize(w, h);
                        camera.aspect = w / h;
                        camera.updateProjectionMatrix();
                    };
                    window.addEventListener('resize', handleResize);
                    
                    return () => {
                        window.removeEventListener('resize', handleResize);
                        window.removeEventListener('mousemove', handleMove);
                    };
                };
                
                const cleanupListeners = initThree();

                return () => {
                    if (cleanupListeners) cleanupListeners();
                    cancelAnimationFrame(animationFrameId);
                    if (renderer) renderer.dispose();
                    if(mountRef.current) mountRef.current.innerHTML = '';
                };
            }, []);

            return (
                <div className="auth-visual">
                    <div ref={mountRef} style={{ position: "fixed", top:0, left:0, width: "50%", height: "100%" }} />
                    <div className="auth-logo-block">
                        <div className="logo-main">
                        <span className="text-sha">Sha</span>
                        <span className="char-g-container">غ</span>
                        <span className="text-alny">alny</span>
                        </div>
                        <div className="logo-below">
                        Empowering teens to work.
                        </div>
                    </div>
                </div>
            );
        };
        
        // ==============================================
        // 2. FORGOT PASSWORD APP
        // ==============================================
        function ForgotPasswordPage() {
            const [email, setEmail] = useState("");
            const [shake, setShake] = useState(false);
            const [error, setError] = useState("");
            const [successMsg, setSuccessMsg] = useState("");
            const [isLoading, setIsLoading] = useState(false);

            useEffect(() => {
                if (error) {
                    const timer = setTimeout(() => setError(""), 3000);
                    return () => clearTimeout(timer);
                }
            }, [error]);

            const handleSubmit = async (e) => {
                e.preventDefault();
                
                if(!email) { 
                    setError("Please enter your email"); 
                    setShake(true); 
                    setTimeout(() => setShake(false), 400); 
                    return; 
                }
                if(!email.includes('@')) {
                    setError("Invalid email address");
                    setShake(true);
                    setTimeout(() => setShake(false), 400);
                    return;
                }

                setIsLoading(true);
                setError("");
                setSuccessMsg("");

                try {
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('action', 'reset_request'); // New action type

                    const response = await fetch('index.php?controller=auth', {
                        method: 'POST',
                        body: formData
                    });

                    const text = await response.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (err) {
                        data = { success: false, message: "Server error" };
                    }

                    if (data.success) {
                        setSuccessMsg("Check your inbox! We've sent a link to reset your password.");
                        setEmail(""); // Clear input
                    } else {
                        // For security, sometimes we show success even if email not found
                        // But for now we display the message
                        setError(data.message || "Failed to send reset link.");
                        setShake(true);
                        setTimeout(() => setShake(false), 400);
                    }
                } catch (err) {
                    setError("Connection error. Please try again.");
                    setShake(true);
                    setTimeout(() => setShake(false), 400);
                } finally {
                    setIsLoading(false);
                }
            };

            return (
                <div className="auth-page">
                    <div className="auth-visual">
                        <LeftVisual />
                    </div>

                    <div className="auth-form-section">
                        <div className="auth-blob-1"></div>
                        <div className="auth-blob-2"></div>

                        <div className="auth-form-wrapper">
                            <div className={`new-form-container ${shake ? 'shake' : ''}`}>
                                <form onSubmit={handleSubmit} className="fade-in">
                                    <div className="mb-6">
                                        <h2 className="text-xl font-bold text-gray-900 mb-1">Reset Password</h2>
                                        <p className="text-gray-500 text-xs">
                                            Enter the email associated with your account and we'll send you a link to reset your password.
                                        </p>
                                    </div>

                                    {error && (
                                        <div className="auth-error">
                                            <p className="auth-error-text">{error}</p>
                                        </div>
                                    )}

                                    {successMsg && (
                                        <div className="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded text-xs">
                                            <p>{successMsg}</p>
                                        </div>
                                    )}

                                    <div className="mb-5">
                                        <label className="form-label">Email Address</label>
                                        <input 
                                            type="text" 
                                            className={`form-input ${error && !email ? 'border-red-500' : ''}`}
                                            value={email} 
                                            onChange={e => setEmail(e.target.value)}
                                            placeholder="Enter your email"
                                            disabled={isLoading}
                                        />
                                    </div>

                                    <button 
                                        type="submit" 
                                        className={`auth-submit-btn ${isLoading ? 'opacity-70 cursor-not-allowed' : ''}`}
                                        disabled={isLoading}
                                    >
                                        {isLoading ? (
                                            <span><i className="fa-solid fa-circle-notch fa-spin mr-2"></i> Sending...</span>
                                        ) : (
                                            "Send Reset Link"
                                        )}
                                    </button>

                                    <div className="auth-link">
                                        Remember your password? 
                                        <a href="index.php?page=login">Back to Login</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            );
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(<ForgotPasswordPage />);
    </script>
</body>
</html>

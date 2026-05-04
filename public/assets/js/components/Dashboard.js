/**
 * Dashboard.js
 * Pure JS React Component (No Babel Required)
 */

(function() {
    // DEBUG START
    console.log("Dashboard.js Started");

    try {
        // --- DEPENDENCY CHECK ---
        const { useState, useRef, useEffect, createElement: h, Fragment } = React;
        
        // Mock Motion to prevent crash if Framer Motion is missing
        const MotionMock = {
            div: (props) => h('div', props, props.children),
            button: (props) => h('button', props, props.children),
        };
        
        let MotionLib = window.Motion;
        if (!MotionLib) {
            console.warn("Framer Motion not loaded, using fallback.");
            MotionLib = {
                motion: MotionMock,
                AnimatePresence: ({children}) => h(Fragment, null, children),
                useMotionValue: () => ({ set: ()=>{} }),
                useSpring: () => ({ get: ()=>0 }),
                useTransform: () => 0
            };
        }
        const { motion, AnimatePresence, useMotionValue, useSpring, useTransform } = MotionLib;

        // --- 1. TILT CARD COMPONENT ---
        function TiltCard({ children, className }) {
            const ref = useRef(null);
            const x = useMotionValue(0);
            const y = useMotionValue(0);

            const mouseXSpring = useSpring(x);
            const mouseYSpring = useSpring(y);

            const rotateX = useTransform(mouseYSpring, [-0.5, 0.5], ["7deg", "-7deg"]);
            const rotateY = useTransform(mouseXSpring, [-0.5, 0.5], ["-7deg", "7deg"]);

            const handleMouseMove = (e) => {
                if (!ref.current) return;
                const rect = ref.current.getBoundingClientRect();
                const width = rect.width;
                const height = rect.height;
                const mouseX = e.clientX - rect.left;
                const mouseY = e.clientY - rect.top;
                const xPct = mouseX / width - 0.5;
                const yPct = mouseY / height - 0.5;
                x.set(xPct);
                y.set(yPct);
            };

            const handleMouseLeave = () => {
                x.set(0);
                y.set(0);
            };

            return h(motion.div, {
                ref: ref,
                onMouseMove: handleMouseMove,
                onMouseLeave: handleMouseLeave,
                style: { rotateX, rotateY, transformStyle: "preserve-3d" },
                className: className,
                initial: { scale: 1 },
                whileHover: { scale: 1.02 }
            }, h("div", { style: { transform: "translateZ(20px)" } }, children));
        }

        // --- 2. AUTH MODAL COMPONENT ---
        const AuthModal = ({ onClose }) => (
            h("div", {
                style: {
                    position: 'fixed', top: 0, left: 0, width: '100%', height: '100%', 
                    background: 'rgba(0,0,0,0.5)', zIndex: 9999, display: 'flex', 
                    alignItems: 'center', justifyContent: 'center'
                },
                onClick: onClose
            }, 
            h(motion.div, {
                initial: { scale: 0.8, opacity: 0 },
                animate: { scale: 1, opacity: 1 },
                style: {
                    background: 'white', padding: '2rem', borderRadius: '16px', 
                    maxWidth: '400px', width: '90%', textAlign: 'center', 
                    boxShadow: '0 20px 25px -5px rgba(0,0,0,0.1)',
                    position: 'relative'
                },
                onClick: (e) => e.stopPropagation()
            }, [
                h("button", {
                    key: "close-btn",
                    onClick: onClose,
                    style: {
                        position: 'absolute', top: '10px', right: '15px',
                        background: 'transparent', border: 'none',
                        color: '#EF4444', fontSize: '1.2rem',
                        cursor: 'pointer', fontWeight: 'bold'
                    }
                }, "✕"),
                h("div", { key: "icon", style: {fontSize: '3rem', marginBottom: '1rem'} }, "🔒"),
                h("h2", { key: "title", style: {fontSize: '1.5rem', fontWeight: 'bold', marginBottom: '0.5rem', color: '#1E293B'} }, "Join Shaغalny"),
                h("p", { key: "text", style: {color: '#64748B', marginBottom: '1.5rem'} }, "Sign up or log in to access jobs, view details, and chat with employers!"),
                h("div", { key: "actions", style: {display: 'flex', gap: '1rem', flexDirection: 'column'} }, [
                    h("a", { key: "login", href: "index.php?page=login", className: "btn btn-grey", style: {justifyContent: 'center', background:'#F1F5F9', color:'#475569', border:'none'} }, "Sign In"),
                    h("a", { key: "register", href: "index.php?page=register", className: "btn btn-blue", style: {justifyContent: 'center'} }, "Create Account")
                ])
            ]))
        );

        // --- 3. TYPEWRITER COMPONENT ---
        const Typewriter = ({ text }) => {
            const [displayText, setDisplayText] = useState('');
            
            useEffect(() => {
                let i = 0;
                setDisplayText('');
                const interval = setInterval(() => {
                    setDisplayText(text.substring(0, i + 1));
                    i++;
                    if (i === text.length) clearInterval(interval);
                }, 100);
                return () => clearInterval(interval);
            }, [text]);

            return h("span", null, displayText);
        };

        // --- 4. MAIN DASHBOARD COMPONENT ---
        function Dashboard() {
            const [activeFilter, setActiveFilter] = useState('All Jobs');
            const [selectedJob, setSelectedJob] = useState(SERVER_DATA.selectedJob);
            const [showAuthModal, setShowAuthModal] = useState(false);
            const mapPins = SERVER_DATA.mapPins;
            const messages = SERVER_DATA.recentMessages;
            
            // --- SCROLL PERSISTENCE ---
            useEffect(() => {
                const savedScroll = localStorage.getItem('shaghalny_scroll_pos');
                if (savedScroll) {
                    setTimeout(() => {
                        window.scrollTo({
                            top: parseInt(savedScroll, 10),
                            behavior: 'instant'
                        });
                    }, 50);
                }

                const handleUnload = () => {
                    localStorage.setItem('shaghalny_scroll_pos', window.scrollY);
                };

                window.addEventListener('beforeunload', handleUnload);
                return () => window.removeEventListener('beforeunload', handleUnload);
            }, []);

            const handleAction = (action) => {
                if (IS_GUEST) {
                    setShowAuthModal(true);
                } else {
                    action();
                }
            };

            const allJobs = SERVER_DATA.recommendedJobs.map(j => ({
                ...j, 
                category: j.title.includes('Dog') ? 'Pet Care' : j.title.includes('Math') ? 'Tutoring' : 'Delivery'
            }));

            const filteredJobs = activeFilter === 'All Jobs' 
                ? allJobs 
                : allJobs.filter(j => j.category === activeFilter);

            // --- Leaflet Map Effect ---
            useEffect(() => {
                if (window.myLeafletMap) return;

                const map = L.map('leaflet-map').setView([30.0444, 31.2357], 13);
                window.myLeafletMap = map; 

                L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Esri, DeLorme, NAVTEQ',
                    maxZoom: 16
                }).addTo(map);

                mapPins.forEach(pin => {
                    const customIcon = L.divIcon({
                        className: 'custom-map-pin',
                        html: `<div style="background:white; padding:5px 10px; border-radius:15px; box-shadow:0 3px 10px rgba(0,0,0,0.2); font-weight:bold; display:flex; gap:5px; align-items:center; white-space:nowrap;">
                                <span>${pin.icon}</span> ${pin.price}
                               </div>`,
                        iconSize: [80, 40],
                        iconAnchor: [40, 20]
                    });

                    const marker = L.marker([pin.lat, pin.lng], {icon: customIcon}).addTo(map);
                    
                    marker.on('click', () => {
                        setSelectedJob(pin); 
                        const fullJob = allJobs.find(j => j.id == pin.id) || 
                                        { ...pin, desc: "Log in to see full details.", rating: "4.8" };
                        setSelectedJob(fullJob);

                        if (window.myLeafletMap) {
                            window.myLeafletMap.flyTo([pin.lat, pin.lng], 16, { animate: true, duration: 1 });
                        }
                    });
                });

                return () => {
                    if (window.myLeafletMap) {
                        window.myLeafletMap.remove();
                        window.myLeafletMap = null;
                    }
                }
            }, []);


            const containerVariants = {
                visible: { 
                    opacity: 1,
                    transition: { staggerChildren: 0.1 }
                }
            };

            const itemVariants = {
                hidden: { y: 20, opacity: 0 },
                visible: { y: 0, opacity: 1 }
            };

            // Main Render
            return h(motion.div, {
                className: "app-wrapper",
                initial: "visible",
                animate: "visible",
                variants: containerVariants
            }, [
                h("div", { key: "b1", className: "bg-blob blob-1" }),
                h("div", { key: "b2", className: "bg-blob blob-2" }),
                h("div", { key: "b3", className: "bg-blob blob-3" }),
                h("div", { key: "b4", className: "bg-blob blob-4" }),
                h("div", { key: "b5", className: "bg-blob blob-5" }),

                // MAIN CONTENT
                h("main", { key: "main", className: "main-container" }, [
                    
                    // 1. WELCOME BANNER
                    h(motion.div, { key: "banner", className: "welcome-banner", variants: itemVariants }, [
                        h("h1", { key: "title", className: "banner-title" }, h(Typewriter, { text: `Welcome back, ${USER_NAME}!` })),
                        h("p", { key: "sub", className: "banner-subtitle" }, "Ready to earn some money today?"),
                        h("div", { key: "actions", className: "banner-actions" }, [
                            h(motion.button, {
                                key: "btn1",
                                whileHover: { scale: 1.05 }, whileTap: { scale: 0.95 },
                                className: "btn btn-green",
                                onClick: () => handleAction(() => window.location.href = 'index.php?action=map')
                            }, [h("i", { key: "icon", className: "fa-solid fa-magnifying-glass" }), " Find Jobs"]),
                            h(motion.button, {
                                key: "btn2",
                                whileHover: { scale: 1.05 }, whileTap: { scale: 0.95 },
                                className: "btn btn-grey",
                                onClick: () => handleAction(() => window.location.href = 'index.php?action=dashboard')
                            }, [h("i", { key: "icon", className: "fa-solid fa-comment" }), " Check Messages"]),
                            h(motion.button, {
                                key: "btn3",
                                whileHover: { scale: 1.05 }, whileTap: { scale: 0.95 },
                                className: "btn btn-white",
                                onClick: () => handleAction(() => window.location.href = 'index.php?action=public_profile')
                            }, [h("i", { key: "icon", className: "fa-solid fa-user" }), " View Profile"])
                        ])
                    ]),

                    // 2. DASHBOARD GRID
                    h("div", { key: "grid", className: "dashboard-grid" }, [
                        
                        // Left: Recommended Jobs
                        h(TiltCard, { key: "left", className: "dash-card" }, [
                            h("div", { key: "head", className: "card-header" }, [
                                h("span", { key: "t" }, "🎯 Recommended for You"),
                                h("span", { key: "f", style: {fontSize:'0.75rem', fontWeight:'400', marginLeft:'auto', color:'#94A3B8'} }, activeFilter)
                            ]),
                            h("div", { key: "list", style: {minHeight: '200px'} }, 
                                h(AnimatePresence, { mode: "popLayout" }, 
                                    filteredJobs.length > 0 ? filteredJobs.map((job, i) => (
                                        h(motion.div, {
                                            className: "list-item",
                                            key: job.title,
                                            layout: true,
                                            initial: { opacity: 0, x: -20 },
                                            animate: { opacity: 1, x: 0 },
                                            exit: { opacity: 0, x: 20 },
                                            whileHover: { scale: 1.02, backgroundColor: "rgba(241, 245, 249, 0.5)", cursor: "pointer" },
                                            whileTap: { scale: 0.98 },
                                            transition: { duration: 0.2 },
                                            onClick: () => {
                                                if (window.myLeafletMap) {
                                                    const lat = 30.0444 + (Math.random() - 0.5) * 0.1;
                                                    const lng = 31.2357 + (Math.random() - 0.5) * 0.1;
                                                    window.myLeafletMap.flyTo([lat, lng], 15, { animate: true, duration: 1.5 });
                                                    L.marker([lat, lng]).addTo(window.myLeafletMap)
                                                        .bindPopup(`<b>${job.title}</b><br>${job.price}`).openPopup();
                                                }
                                                setSelectedJob({ ...job, desc: "Detailed description loaded dynamically..." });
                                            }
                                        }, [
                                            h("div", { key: "l", className: "item-left" }, [
                                                h("div", { key: "i", className: `item-icon ${job.bg}` }, job.icon),
                                                h("div", { key: "t" }, [
                                                    h("div", { key: "ti", className: "item-title" }, job.title),
                                                    h("div", { key: "ts", className: "item-sub" }, job.dist)
                                                ])
                                            ]),
                                            h("div", { key: "r", className: "item-right" }, job.price)
                                        ])
                                    )) : h(motion.div, { key: "empty", initial: { opacity: 0 }, animate: { opacity: 1 }, className: "text-center py-8 text-gray-400" }, "No jobs found for this category.")
                                )
                            ),
                            h(motion.button, { key: "view", whileHover: { scale: 1.02 }, whileTap: { scale: 0.98 }, className: "btn-block", onClick: () => handleAction(() => console.log('Viewing all jobs...')) }, "View All Jobs")
                        ]),

                        // Right: Recent Messages
                        h(TiltCard, { key: "right", className: "dash-card" }, [
                            h("div", { key: "head", className: "card-header" }, h("span", null, "💬 Recent Messages")),
                            messages.map((msg, i) => (
                                h(motion.div, { key: i, className: "list-item", whileHover: { scale: 1.02, backgroundColor: "rgba(241, 245, 249, 0.5)", cursor: "pointer" }, whileTap: { scale: 0.98 } }, [
                                    h("div", { key: "l", className: "item-left" }, [
                                        h("div", { key: "i", className: `item-icon bg-${msg.color}-100 text-${msg.color}-600 font-bold` }, msg.initial),
                                        h("div", { key: "t" }, [
                                            h("div", { key: "n", className: "item-title" }, msg.name),
                                            h("div", { key: "m", className: "item-sub" }, msg.msg)
                                        ])
                                    ]),
                                    h("div", { key: "r", className: "item-sub" }, msg.time)
                                ])
                            )),
                            h(motion.button, { key: "view", whileHover: { scale: 1.02 }, whileTap: { scale: 0.98 }, className: "btn-block", onClick: () => handleAction(() => console.log('Viewing all messages...')) }, "View All Messages")
                        ])
                    ]),
                    
                    // 3. MAP SECTION
                    h(motion.div, { key: "map", className: "map-section", variants: itemVariants }, [
                        h("div", { key: "head", className: "card-header" }, h("span", null, "📍 Local Jobs Near You")),
                        h("div", { key: "filter", className: "filter-bar", style: {overflowX: 'auto', whiteSpace: 'nowrap', paddingBottom: '10px'} }, 
                            ['All Jobs', 'Pet Care', 'Tutoring', 'Delivery', 'Yard Work'].map(filter => (
                                h(motion.div, {
                                    key: filter,
                                    whileHover: { scale: 1.05 }, whileTap: { scale: 0.95 },
                                    className: `filter-chip ${activeFilter === filter ? 'active' : ''}`,
                                    onClick: () => handleAction(() => setActiveFilter(filter))
                                }, [
                                    filter === 'All Jobs' ? '' : filter === 'Pet Care' ? '🐕 ' : filter === 'Delivery' ? '🚚 ' : filter === 'Tutoring' ? '📚 ' : '🌱 ',
                                    filter
                                ])
                            ))
                        ),
                        h("div", { key: "container", className: "map-container" }, [
                            // Leaflet Map Div
                            h("div", { key: "leaflet", id: "leaflet-map", className: "visual-map" }),
                            
                            // Job Detail Card
                            h("div", { key: "detail", className: "job-detail-card" }, 
                                selectedJob ? [
                                    h("h3", { key: "t", className: "font-bold text-xl mb-1" }, selectedJob.title),
                                    h("div", { key: "p", className: "detail-price" }, selectedJob.price),
                                    h("p", { key: "d", className: "text-gray-400 text-sm mb-4" }, selectedJob.dist),
                                    h("p", { key: "desc", className: "text-gray-600 mb-6 leading-relaxed" }, selectedJob.desc),
                                    h("div", { key: "meta", className: "flex items-center justify-between text-sm text-gray-500 mb-6" }, [
                                        h("span", { key: "auth" }, "Posted by: Emma K."),
                                        h("span", { key: "rating", className: "font-bold text-yellow-500" }, `★ ${selectedJob.rating || 'N/A'}`)
                                    ]),
                                    h(motion.button, { key: "apply", whileHover: { scale: 1.02 }, whileTap: { scale: 0.98 }, className: "btn-block", onClick: () => handleAction(() => alert('Applying...')) }, "Apply Now")
                                ] : h("div", { key: "none", className: "text-center text-gray-400 py-10" }, [
                                    h("div", { key: "icon", className: "text-4xl mb-2" }, "🔍"),
                                    h("p", { key: "txt" }, "Select a job from the list to see details.")
                                ])
                            )
                        ])
                    ])

                ]),

                // AUTH MODAL
                h(AnimatePresence, { key: "modal" }, 
                    showAuthModal ? h(AuthModal, { onClose: () => setShowAuthModal(false) }) : null
                )
            ]);
        }

        // --- MOUNT ---
        class ErrorBoundary extends React.Component {
            constructor(props) {
                super(props);
                this.state = { hasError: false, error: null };
            }
            static getDerivedStateFromError(error) { return { hasError: true, error }; }
            render() {
                if (this.state.hasError) {
                    return h('div', { className: "p-4 bg-red-50 text-red-600" }, [
                        h('h3', { key: "h" }, "Error in Dashboard"),
                        h('pre', { key: "p" }, this.state.error.toString())
                    ]);
                }
                return this.props.children;
            }
        }

        const root = ReactDOM.createRoot(document.getElementById('root'));
        root.render(h(ErrorBoundary, null, h(Dashboard)));

    } catch (err) {
        console.error(err);
        const rootEl = document.getElementById('root');
        if (rootEl) rootEl.innerHTML = '<div style="color:red; padding:20px; border:2px solid red;">CRITICAL JS ERROR: ' + err.message + '</div>';
    }
})();

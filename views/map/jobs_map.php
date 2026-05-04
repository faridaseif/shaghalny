<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Map - Shaghalny</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/css/Header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    
    <!-- React (CDNJS) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/18.2.0/umd/react.development.js" crossorigin></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react-dom/18.2.0/umd/react-dom.development.js" crossorigin></script>
    <!-- Framer Motion -->
    <script src="https://unpkg.com/framer-motion@10.16.4/dist/framer-motion.js"></script>

    <style>
        /* MAP STYLING - CRITICAL FOR IT TO SHOW UP */
        #map {
            height: 500px;
            width: 100%;
            border-radius: 12px;
            z-index: 1;
        }

        /* Container Styling */
        .map-wrapper {
            background: linear-gradient(135deg, #7F5AF0 0%, #5d3ebc 100%); /* Purple theme */
            padding: 15px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(100, 50, 200, 0.2);
        }

        /* Detail Card Styling */
        .job-details-card {
            height: 500px;
            background: white;
            border-radius: 20px;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid #eee;
        }

        /* Custom Marker Icon Style */
        .price-badge-marker {
            background-color: white;
            border: 2px solid #7F5AF0;
            border-radius: 20px;
            padding: 2px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            white-space: nowrap;
            width: auto !important; 
            height: auto !important;
        }

        /* Filter Buttons Active State */
        .filter-btn.active {
            background-color: #7F5AF0; /* Purple */
            color: white;
            border-color: #7F5AF0;
        }
    </style>
</head>
<body class="bg-light">
    
    <?php require_once APP_ROOT . '/app/views/layouts/header.php'; ?>

    <!-- <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="#">Shaghalny</a>
        </div>
    </nav> -->

    <div class="container mb-5">
        
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h3 class="fw-bold">🗺️ Local Jobs Near You</h3>
            </div>
            <div class="col-md-6 text-md-end">
                <button onclick="filterMap('All', this)" class="btn btn-outline-secondary rounded-pill filter-btn active">All Jobs</button>
                <button onclick="filterMap('pet care', this)" class="btn btn-outline-secondary rounded-pill filter-btn">🐕 Pet Care</button>
                <button onclick="filterMap('tutoring', this)" class="btn btn-outline-secondary rounded-pill filter-btn">📚 Tutoring</button>
                <button onclick="filterMap('errands', this)" class="btn btn-outline-secondary rounded-pill filter-btn">📦 Errands</button>
                <button onclick="filterMap('yard work', this)" class="btn btn-outline-secondary rounded-pill filter-btn">🌱 Yard Work</button>
                <button onclick="filterMap('other', this)" class="btn btn-outline-secondary rounded-pill filter-btn">📋 Other</button>
            </div>
        </div>

        <div class="row g-3">
            
            <div class="col-lg-8">
                <div class="map-wrapper position-relative">
                    <div id="map"></div>
                    <!-- LOCATE ME BUTTON -->
                    <button class="btn btn-light shadow-sm position-absolute" 
                            style="bottom: 20px; right: 20px; z-index: 999; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;" 
                            onclick="locateUser()"
                            title="Locate Me">
                        📍
                    </button>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="job-details-card shadow-sm" id="details-panel">
                    
                    <div id="view-empty" class="text-center text-muted">
                        <div style="font-size: 50px; margin-bottom: 20px;">📍</div>
                        <h4>Explore the Map</h4>
                        <p>Click on any job price on the map<br>to see full details here.</p>
                    </div>

                    <div id="view-content" style="display: none;">
                        <span id="d-type" class="badge bg-primary mb-2">Category</span>
                        <h3 id="d-title" class="fw-bold mb-2">Job Title</h3>
                        <h2 class="text-success fw-bold mb-3" id="d-price">$00</h2>
                        
                        <p class="text-muted" id="d-desc">Description goes here.</p>
                        
                        <div class="mt-auto">
                            <hr>
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light rounded-circle p-2 me-2">👤</div>
                                <div>
                                    <small class="text-muted d-block">Posted by</small>
                                    <span class="fw-bold">Alex M.</span>
                                </div>
                            </div>
                            <a id="apply-btn" href="#" class="btn btn-primary w-100 py-2 fw-bold">Apply Now 🚀</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <div class="row mt-5" id="jobs-list-container">
            </div>

    </div>

    <!-- Job Description Modal -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content rounded-4">

      <div class="modal-header">
        <h5 class="modal-title" id="modalJobTitle"></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <span id="modalJobType" class="badge bg-primary mb-2"></span>
        <h4 class="text-success fw-bold mb-3" id="modalJobPrice"></h4>
        <p id="modalJobDesc" class="text-muted"></p>
        <p class="small text-muted" id="modalJobLocation"></p>
      </div>

      <div class="modal-footer">
        <a id="modalApplyBtn" href="#" class="btn btn-primary fw-bold">
          Apply Now 🚀
        </a>
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 text-center p-4">
      <div class="modal-body">
        <div class="mb-3">
          <span style="font-size: 3rem;">🎉</span>
        </div>
        <h4 class="fw-bold text-success mb-3">Application Sent!</h4>
        <p class="text-muted mb-4">You have successfully applied for this job. Good luck!</p>
        <button type="button" class="btn btn-primary px-4 py-2" data-bs-dismiss="modal">
          Awesome
        </button>
      </div>
    </div>
  </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
    // --- 1. GLOBAL SETUP ---
    let allJobs = []; 
    let markersLayer = null;
    let jobMarkers = {}; // Object to store markers {jobId: markerObject}

    // Nominatim (OpenStreetMap) API endpoint for geocoding
    const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=';

    // --- 2. INITIALIZE MAP ---
    const map = L.map('map').setView([30.0444, 31.2357], 11); // Default to Cairo (Zoomed out to see New Cairo/Giza)
    
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    markersLayer = L.layerGroup().addTo(map);

    // --- 3. ASYNCHRONOUS DATA LOADING (Start Geocoding) ---
    async function loadJobData() {
        try {
            // Injected from PHP controller
            const data = <?php echo json_encode($jobs ?? []); ?>;
            allJobs = data; 
            
            console.log(`✅ Job Data fetched: ${allJobs.length} jobs.`);

            // 1. Render the job list cards immediately (so user sees them while map loads)
            renderJobList(allJobs); 

            // 2. Start geocoding and rendering for each job SEQUENTIALLY with delay
            // Nominatim Rate Limit: Max 1 request per second
            for (const job of allJobs) {
                 await geocodeAndRenderJob(job);
                 // 1.2 second delay between requests
                 await new Promise(r => setTimeout(r, 1200)); 
            } 

        } catch (error) {
            console.error('❌ CRITICAL: Failed to load job data:', error);
            alert('Could not load jobs from the server. Check the browser console.');
        }
    }

    // --- NEW: GEOCoding Function ---
    async function geocodeAndRenderJob(job) {
        const address = job.location_string || job.location;
        let lat, lng, isApproximate = false;

        // 1. Try Geocoding
        if (address) {
            // CACHE CHECK: Check LocalStorage first to avoid API limits
            const cacheKey = 'shaghalny_geo_cache';
            let geoCache = JSON.parse(localStorage.getItem(cacheKey) || '{}');
            const queryAddr = address.toLowerCase().includes('egypt') ? address : address + ', Egypt';

            if (geoCache[queryAddr]) {
                // Cache Hit!
                lat = geoCache[queryAddr].lat;
                lng = geoCache[queryAddr].lng;
                console.log(`📍 Cache hit for: ${queryAddr}`);
            } else {
                // Cache Miss - Call API
                try {
                    const response = await fetch(NOMINATIM_URL + encodeURIComponent(queryAddr));
                    const results = await response.json();
                    if (results && results.length > 0) {
                        lat = parseFloat(results[0].lat);
                        lng = parseFloat(results[0].lon);
                        
                        // Save to Cache
                        geoCache[queryAddr] = { lat, lng };
                        localStorage.setItem(cacheKey, JSON.stringify(geoCache));
                    }
                } catch (error) {
                    console.warn(`Geocoding error for ${job.job_id}:`, error);
                }
            }
        }

        // 2. Fallback if geocoding failed
        if (!lat || !lng) {
            console.warn(`Using fallback location for job ID ${job.job_id} ("${address}")`);
            isApproximate = true;
            // Random offset around Cairo (30.0444, 31.2357)
            lat = 30.0444 + (Math.random() - 0.5) * 0.1; 
            lng = 31.2357 + (Math.random() - 0.5) * 0.1;
        }

        // 3. Render Marker
        const job_typeIcon = L.divIcon({
            className: 'price-badge-marker',
            html: `${job.category || 'N/A'}`, 
            iconAnchor: [20, 10] 
        });

        const marker = L.marker([lat, lng], { icon: job_typeIcon });
        
        const popupContent = `
            <b>${job.title || job.category}</b>
            <br>Price: ${job.payment ? job.payment + ' EGP' : 'N/A'}
            <br>Location: ${address || 'Unknown'} ${isApproximate ? '(Approximate)' : ''}
        `;
        marker.bindPopup(popupContent);
        
        jobMarkers[job.job_id] = marker; 
        
        marker.on('click', () => {
            showJobDetails(job);
        });
        
        markersLayer.addLayer(marker);
        
        // Optional: Recenter if it's the first real one
        if (!isApproximate && markersLayer.getLayers().length === 1) {
             map.setView([lat, lng], 13);
        }
    }
    
    // --- 5. MAIN FUNCTION: RENDER JOBS LIST (renamed) ---
    // This is now only responsible for rendering the list cards.
    function renderJobList(jobsToRender) {
        // A. Clear list
        const listContainer = document.getElementById('jobs-list-container');
        listContainer.innerHTML = '<h5 class="mb-3">Available Jobs List</h5>'; 

        if(jobsToRender.length === 0) {
            listContainer.innerHTML += '<p class="text-muted">No jobs found for this category.</p>';
            return;
        }

        // B. Loop and render list items
        jobsToRender.forEach(job => {
            const col = document.createElement('div');
            col.className = 'col-md-3 mb-4';
            // IMPORTANT: List click now calls handleJobClick (defined below)
            col.innerHTML = `
                <div class="card h-100 shadow-sm border-0" onclick="handleJobClick(${job.job_id})" style="cursor: pointer;">
                    <div class="card-body">
                        <h6 class="fw-bold">${job.category}</h6>
                        <div class="text-success fw-bold mb-2">${job.payment ? job.payment + ' EGP' : 'N/A'}</div>
                        <small class="text-muted">${job.location_string || job.location || job.description}</small>
                    </div>
                </div>
            `;
            listContainer.appendChild(col);
        });
    }

    // --- NEW FUNCTION: HANDLE CLICK FROM LIST ---
    function handleJobClick(jobId) {
        const job = allJobs.find(j => j.job_id == jobId); 
        
        if (!job) return;

        showJobDetails(job); // Open the details sidebar

        openJobModal(job);

        const marker = jobMarkers[jobId];

        if (marker) {
            // Center the map and open the popup
            map.flyTo(marker.getLatLng(), 15, { duration: 0.5 }); 
            marker.openPopup();
        } else {
             // If the marker hasn't loaded or geocoding failed, tell the user
            alert(`Job location for "${job.title}" is still loading or could not be found.`);
        }
    }

    // --- 6. FUNCTION: SHOW SIDE PANEL (Updated to use location_string) ---
    function showJobDetails(job) {
        if (typeof job === 'number') {
            job = allJobs.find(j => j.job_id == job);
            if (!job) return; 
        }

        document.getElementById('view-empty').style.display = 'none';
        const content = document.getElementById('view-content');
        content.style.display = 'block';

        // Fill Data
        document.getElementById('d-title').textContent = job.title || job.category; 
        document.getElementById('d-price').textContent = job.payment ? job.payment + ' EGP' : 'N/A';
        document.getElementById('d-desc').textContent = job.description;
        document.getElementById('d-type').textContent = job.category;
        
        // Update Apply Link with Job ID
        // Update Apply Link with Job ID
        const applyBtn = document.getElementById('apply-btn');
        if (applyBtn) {
            const newHref = `index.php?controller=Application&action=apply&job_id=${job.job_id}`;
            console.log('Setting apply link to:', newHref);
            applyBtn.href = newHref;
        } else {
            console.error('Apply button not found!');
        } 
    }

    function openJobModal(job) {
    document.getElementById('modalJobTitle').textContent =
        job.title || job.category;

    document.getElementById('modalJobType').textContent =
        job.category;

    document.getElementById('modalJobPrice').textContent =
        job.payment ? job.payment + ' EGP' : 'N/A';

    document.getElementById('modalJobDesc').textContent =
        job.description;

    document.getElementById('modalJobLocation').textContent =
        '📍 ' + (job.location_string || job.location || 'Location not specified');

    document.getElementById('modalApplyBtn').href =
        `index.php?controller=Application&action=apply&job_id=${job.job_id}`;

    const modal = new bootstrap.Modal(document.getElementById('jobModal'));
    modal.show();
}

  document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);

    if (params.get('applied') === '1') {
        // Show Success Modal instead of Alert
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();

        // Remove the flag so it doesn’t show again on refresh
        window.history.replaceState(
            {},
            document.title,
            window.location.pathname + '?controller=Map&action=map'
        );
    }
    });

    // --- 7. FUNCTION: FILTER BUTTONS (Updated to call renderJobList) ---
    function filterMap(category, btnElement) {
        document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
        btnElement.classList.add('active');

        let filteredJobs;
        if (category === 'All') {
            filteredJobs = allJobs;
        } else {
            filteredJobs = allJobs.filter(job => job.category === category);
        }

        // Re-render only the list cards (markers remain on the map)
        renderJobList(filteredJobs); 
        
        // OPTIONAL: Filter markers here if performance allows
        // markersLayer.clearLayers();
        // filteredJobs.forEach(job => jobMarkers[job.id]?.addTo(markersLayer));
    }

    function locateUser() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser.');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Add "You are Here" Marker
                const userIcon = L.divIcon({
                    className: 'user-marker',
                    html: '<div style="background-color: #007bff; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(0,0,0,0.5);"></div>',
                    iconAnchor: [10, 10]
                });

                L.marker([lat, lng], { icon: userIcon }).addTo(map)
                    .bindPopup('<b>You are here</b>').openPopup();

                map.setView([lat, lng], 14);
            },
            (error) => {
                console.error("Geolocation error:", error);
                alert('Could not get your location. Please check browser permissions.');
            }
        );
    }

    // --- 8. INITIAL KICKOFF ---
    loadJobData(); 
</script>
  
  <?php require_once APP_ROOT . '/app/views/layouts/footer.php'; ?>
</body>
</html>
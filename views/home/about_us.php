<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us – Shaghalny</title>
<!-- Tailwind CSS -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#22C55E', // Official Logo Green from Header.css
            secondary: '#1E293B', // Dark Navy from Header.css
            accent: '#F97316', // Orange from Header.css
          }
        }
      }
    }
</script>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- React & Babel -->
<script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/@babel/standalone/babel.min.js"></script>

<link rel="stylesheet" href="assets/css/Header.css">
<link rel="stylesheet" href="assets/css/global.css">
<link rel="stylesheet" href="assets/css/footer.css">

<style>
    body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
    /* Header Root Wrapper to prevent Tailwind conflicts if any */
    #header-root { z-index: 50; position: relative; }
</style>
</head>

<body class="bg-slate-50 text-slate-800">

    <!-- Shared Header -->
    <?php include __DIR__ . '/../layouts/header.php'; ?>

    <!-- HERO SECTION -->
    <div class="relative bg-secondary text-white overflow-hidden">
        <!-- Abstract Background -->
        <div class="absolute inset-0 opacity-20">
             <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary rounded-full blur-3xl"></div>
             <div class="absolute bottom-0 left-0 w-72 h-72 bg-accent rounded-full blur-3xl"></div>
        </div>

        <div class="relative container mx-auto px-6 py-24 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-tight">About <span class="text-primary">Shaغalny</span></h1>
            <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Your gateway to small jobs, fast earnings, and trusted opportunities. Building the next generation of leaders in Egypt.
            </p>
        </div>
    </div>

    <!-- OUR STORY -->
    <div class="container mx-auto px-6 py-16">
        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 border border-slate-100 relative overflow-hidden">
             <div class="absolute top-0 left-0 w-2 h-full bg-primary"></div>
             <div class="md:flex items-center gap-12">
                 <div class="md:w-1/2 mb-8 md:mb-0">
                     <h2 class="text-3xl font-bold text-secondary mb-6">Our Story</h2>
                     <p class="text-slate-600 leading-relaxed mb-4">
                        Shaghalny started with one simple idea: <strong class="text-secondary">every small job matters</strong>.  
                        Whether someone needs help walking their dog, tutoring their child,
                        or delivering something urgent — there should be a trusted place where
                        people can find help quickly, safely, and affordably.
                     </p>
                     <p class="text-slate-600 leading-relaxed">
                        We connect <strong class="text-secondary">real people with real opportunities</strong> in a fast, smart, and modern way.
                     </p>
                 </div>
                 <div class="md:w-1/2 grid grid-cols-2 gap-4">
                      <!-- Visual Interest Grid -->
                      <div class="bg-slate-100 rounded-lg h-32 w-full flex items-center justify-center text-4xl">🚀</div>
                      <div class="bg-primary text-white rounded-lg h-32 w-full flex items-center justify-center text-4xl">🤝</div>
                      <div class="bg-orange-50 rounded-lg h-32 w-full flex items-center justify-center text-4xl">🇪🇬</div>
                      <div class="bg-blue-50 rounded-lg h-32 w-full flex items-center justify-center text-4xl">💼</div>
                 </div>
             </div>
        </div>
    </div>

    <!-- WHAT WE STAND FOR -->
    <div class="container mx-auto px-6 py-8">
        <h2 class="text-3xl font-bold text-center text-secondary mb-12">What We Stand For</h2>
        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition text-center group">
                <div class="w-16 h-16 mx-auto bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Trust</h3>
                <p class="text-sm text-slate-500">Verified job postings and transparent details for your safety.</p>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition text-center group">
                <div class="w-16 h-16 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fa-solid fa-universal-access"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Accessibility</h3>
                <p class="text-sm text-slate-500">Built so everyone can earn extra income and everyone can get help.</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition text-center group">
                <div class="w-16 h-16 mx-auto bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-purple-600 group-hover:text-white transition">
                   <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Community</h3>
                <p class="text-sm text-slate-500">Connecting neighbors and building a stronger local economy.</p>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 hover:shadow-md transition text-center group">
                 <div class="w-16 h-16 mx-auto bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-2xl mb-4 group-hover:bg-orange-600 group-hover:text-white transition">
                    <i class="fa-solid fa-scale-balanced"></i>
                 </div>
                <h3 class="text-xl font-bold mb-2">Fairness</h3>
                <p class="text-sm text-slate-500">Simple pricing, no hidden fees, and fair wages for all tasks.</p>
            </div>
        </div>
    </div>

    <!-- CTA BANNER -->
    <div class="container mx-auto px-6 py-16">
        <div class="bg-gradient-to-r from-secondary to-slate-800 rounded-3xl p-10 text-center relative overflow-hidden">
             <!-- Decorative Circles -->
             <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-16 -mt-16"></div>
             <div class="absolute bottom-0 left-0 w-40 h-40 bg-white/5 rounded-full -ml-10 -mb-10"></div>
             
             <h2 class="text-3xl font-bold text-white mb-4 relative z-10">Ready to Get Started?</h2>
             <p class="text-slate-300 mb-8 max-w-xl mx-auto relative z-10">Join thousands of teens and parents using Shaghalny today.</p>
             <a href="index.php?action=register" class="inline-block bg-primary hover:bg-green-600 text-white font-bold py-3 px-8 rounded-full transition transform hover:scale-105 shadow-lg relative z-10">
                 Join Now
             </a>
        </div>
    </div>

    <!-- Unified Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

</body>
</html>
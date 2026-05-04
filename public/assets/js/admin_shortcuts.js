/**
 * Admin Shortcut Handler
 * Shortcut: Ctrl + Shift + A
 * Action: Redirects to Admin Login
 */
console.log("Admin shortcuts script loaded");

document.addEventListener('keydown', function (event) {
  // Debug key presses
  // console.log("Key pressed:", event.key, "Ctrl:", event.ctrlKey, "Shift:", event.shiftKey);

  // Check for Ctrl + Shift + A
  if (event.ctrlKey && event.shiftKey && (event.key === 'a' || event.key === 'A')) {
    event.preventDefault(); // Prevent any default browser behavior
    
    // Simple relative redirect works best for this router structure
    window.location.href = 'index.php?controller=admin&action=login';
  }
});

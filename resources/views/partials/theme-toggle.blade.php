<style>
.theme-toggle-fixed{position:fixed;right:16px;bottom:16px;z-index:1050;display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:50%;border:none;background:#111;color:#fff;box-shadow:0 4px 12px rgba(0,0,0,.2);cursor:pointer}
.theme-toggle-fixed.light{background:#fff;color:#111;border:1px solid #e5e7eb}
.theme-toggle-fixed i{font-size:18px}

/* Dark theme base */
[data-theme='dark'] body{background-color:#0b0f13 !important;color:#e5e7eb}

/* Sidebar */
[data-theme='light'] #leftsidebar.sidebar,[data-theme='light'] .sidebar{background-color:#ffffff;color:#1f2937}
[data-theme='light'] .menu .list>li>a,[data-theme='light'] .menu .list>li>a span,[data-theme='light'] .menu .list .ml-menu li a,[data-theme='light'] .menu .list .ml-menu li a span{color:#1f2937}
[data-theme='light'] .menu .list>li>a:hover,[data-theme='light'] .menu .list>li>a:focus{background-color:rgba(15,23,42,0.06);color:#0f172a}
[data-theme='light'] .menu .list>li.active>a{background-color:#e2e8f0;color:#0f172a}
[data-theme='light'] .menu .list>li.active>a span{color:#0f172a}
[data-theme='light'] .menu .list li a .feather{color:#475569}
[data-theme='light'] .sidebar-userpic-name,[data-theme='light'] .profile-usertitle-job,[data-theme='light'] .sidebar-userpic-btn a{color:#1f2937}
[data-theme='light'] .navbar{background-color:#1f2522 !important;color:#1f2937}
[data-theme='light'] .navbar .navbar-brand{color:#0f172a !important}
[data-theme='light'] .navbar .navbar-brand .logo-name{color:#0f172a !important}
[data-theme='light'] .navbar .navbar-brand:hover .logo-name{color:#2563eb !important}
[data-theme='light'] .navbar .bars,[data-theme='light'] .navbar .navbar-nav>li>a,[data-theme='light'] .navbar .navbar-nav>li>a i,[data-theme='light'] .nav>li>a,[data-theme='light'] .nav>li>a i,[data-theme='light'] .nav>li>a .material-icons,[data-theme='light'] .nav>li>a .fas,[data-theme='light'] .nav>li>a .far{color:#ffffff !important}

[data-theme='light'] .navbar .navbar-nav > li.active > a,
[data-theme='light'] .nav > li.active > a {font-weight: bold !important;font-size: 0.6rem !important;}
[data-theme='light'] .navbar .dropdown-menu{background-color:#f8fafc !important;border-color:#cbd5f5;box-shadow:0 10px 25px rgba(15,23,42,0.08)}
[data-theme='light'] .navbar .dropdown-menu a{color:#1f2937 !important}
[data-theme='light'] .navbar .dropdown-menu a:hover{background-color:#e2e8f0 !important;color:#0f172a !important}

[data-theme='dark'] #leftsidebar.sidebar,[data-theme='dark'] .sidebar{background-color:#0f172a !important;color:#e5e7eb}
[data-theme='dark'] .menu .list>li>a{color:#cbd5e1}
[data-theme='dark'] .menu .list>li>a span,[data-theme='dark'] .menu .list .ml-menu li a,[data-theme='dark'] .menu .list .ml-menu li a span{color:#e5e7eb}
[data-theme='dark'] .menu .list>li.active>a,[data-theme='dark'] .menu .list>li>a:focus,[data-theme='dark'] .menu .list>li>a:hover{background-color:#1f2937;color:#fff}
[data-theme='dark'] .menu .list>li.active>a span{color:#38bdf8}
[data-theme='dark'] .menu .list li a .feather{color:#94a3b8}
[data-theme='dark'] .ml-menu li a{color:#cbd5e1}
[data-theme='dark'] .sidebar-userpic-name,[data-theme='dark'] .profile-usertitle-job,[data-theme='dark'] .sidebar-userpic-btn a{color:#e5e7eb}
[data-theme='dark'] .navbar{background-color:#0b1120 !important;color:#cbd5e1}
[data-theme='dark'] .navbar .navbar-brand{color:#38bdf8 !important}
[data-theme='dark'] .navbar .navbar-brand .logo-name{color:#38bdf8 !important}
[data-theme='dark'] .navbar .navbar-brand:hover .logo-name{color:#60a5fa !important}
[data-theme='dark'] .navbar .bars,[data-theme='dark'] .navbar .navbar-nav>li>a,[data-theme='dark'] .navbar .navbar-nav>li>a i,[data-theme='dark'] .nav>li>a,[data-theme='dark'] .nav>li>a i,[data-theme='dark'] .nav>li>a .material-icons,[data-theme='dark'] .nav>li>a .fas,[data-theme='dark'] .nav>li>a .far{color:#cbd5e1 !important}
[data-theme='dark'] .navbar .navbar-nav>li>a:hover,[data-theme='dark'] .navbar .navbar-nav>li>a:focus,[data-theme='dark'] .nav>li>a:hover,[data-theme='dark'] .nav>li>a:focus{color:#60a5fa !important}
[data-theme='dark'] .navbar .dropdown-menu{background-color:#111827 !important;border-color:#1f2937 !important;box-shadow:0 12px 24px rgba(8,12,24,0.4)}
[data-theme='dark'] .navbar .dropdown-menu a{color:#e5e7eb !important}
[data-theme='dark'] .navbar .dropdown-menu a:hover{background-color:#1f2937 !important;color:#60a5fa !important}

/* Cards / panels */
[data-theme='dark'] .card{background-color:#0f172a;color:#e5e7eb;border-color:#1f2937}
[data-theme='dark'] .card .card-header,[data-theme='dark'] .card .header{background-color:#111827;color:#e5e7eb;border-bottom:1px solid #1f2937}

/* Tables */
[data-theme='dark'] table{color:#e5e7eb}
[data-theme='dark'] .table thead th{border-color:#374151}
[data-theme='dark'] .table td,[data-theme='dark'] .table th{border-color:#1f2937}

/* Forms */
[data-theme='dark'] .form-control,[data-theme='dark'] .form-select{background-color:#111827;border-color:#374151;color:#e5e7eb}
[data-theme='dark'] .form-control:focus,[data-theme='dark'] .form-select:focus{background-color:#0b1220;border-color:#60a5fa;color:#fff;box-shadow:none}
[data-theme='dark'] label{color:#e5e7eb}

/* Modals */
[data-theme='dark'] .modal-content{background-color:#0f172a !important;color:#e5e7eb !important;border:1px solid #1f2937 !important}
[data-theme='dark'] .modal-header{background-color:#0f172a !important;color:#e5e7eb !important;border-bottom:1px solid #1f2937 !important}
[data-theme='dark'] .modal-body{background-color:#0f172a !important;color:#e5e7eb !important}
[data-theme='dark'] .modal-footer{background-color:#0f172a !important;color:#e5e7eb !important;border-top:1px solid #1f2937 !important}
[data-theme='dark'] .modal-title{color:#e5e7eb !important}
[data-theme='dark'] .btn-close{filter:invert(1) grayscale(100%) brightness(200%) !important}
[data-theme='dark'] .form-check-input{background-color:#111827 !important;border-color:#374151 !important}
[data-theme='dark'] .form-check-input:checked{background-color:#3b82f6 !important;border-color:#3b82f6 !important}
[data-theme='dark'] .form-check-label{color:#e5e7eb !important}
[data-theme='dark'] .input-group-text{background-color:#111827 !important;border-color:#374151 !important;color:#e5e7eb !important}

/* Light theme modals */
[data-theme='light'] .modal-content{background-color:#ffffff !important;color:#1f2937 !important;border:1px solid #e5e7eb !important}
[data-theme='light'] .modal-header{background-color:#ffffff !important;color:#1f2937 !important;border-bottom:1px solid #e5e7eb !important}
[data-theme='light'] .modal-body{background-color:#ffffff !important;color:#1f2937 !important}
[data-theme='light'] .modal-footer{background-color:#ffffff !important;color:#1f2937 !important;border-top:1px solid #e5e7eb !important}

/* Navbars (basic) */
</style>
<button id="themeToggle" class="theme-toggle-fixed" aria-label="Toggle theme">
    <i class="fas fa-moon"></i>
 </button>
<script>
(function(){
  const STORAGE_KEY='p2e-theme';
  const root=document.documentElement; // <html>
  const saved=localStorage.getItem(STORAGE_KEY)||'light';
  setTheme(saved);
  const btn=document.getElementById('themeToggle');
  updateBtn(saved);
  btn.addEventListener('click',()=>{
    const cur=root.getAttribute('data-theme')==='dark'?'dark':'light';
    const next=cur==='dark'?'light':'dark';
    setTheme(next);localStorage.setItem(STORAGE_KEY,next);updateBtn(next);
    
    // Dispatch theme change event for modals
    document.dispatchEvent(new CustomEvent('themeChange', {
      detail: { theme: next }
    }));
  });
  function setTheme(mode){ 
    root.setAttribute('data-theme',mode); 
    document.body.classList.toggle('dark', mode==='dark'); 
    
    // Update all existing modals
    document.querySelectorAll('.modal').forEach(modal => {
      modal.setAttribute('data-theme', mode);
      const modalContent = modal.querySelector('.modal-content');
      if (modalContent) {
        modalContent.setAttribute('data-theme', mode);
      }
    });
  }
  function updateBtn(mode){
    btn.classList.toggle('light', mode==='light');
    const icon=btn.querySelector('i');
    if(mode==='dark'){ icon.className='fas fa-sun'; }
    else { icon.className='fas fa-moon'; }
  }
})();
</script>

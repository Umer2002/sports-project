
  document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".menu-bar a");  // All tab links
    const tabContents = document.querySelectorAll(".tab-pane");  // All tab contents

    // Function to show the clicked tab and hide others
    function activateTab(targetId) {
      tabs.forEach(tab => tab.classList.remove("active"));
      tabContents.forEach(content => content.classList.remove("show", "active"));

      const targetTab = document.querySelector(`a[href="${targetId}"]`);
      const targetContent = document.querySelector(targetId);

      targetTab.classList.add("active");
      targetContent.classList.add("show", "active");
    }

    // Initialize by showing the first tab content
    activateTab("#donation");

    // Add event listeners to tabs
    tabs.forEach(tab => {
      tab.addEventListener("click", function(e) {
        e.preventDefault();  // Prevent the default anchor link behavior
        const targetId = tab.getAttribute("href");
        activateTab(targetId);
      });
    });
  });

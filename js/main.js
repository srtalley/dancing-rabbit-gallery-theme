//version: 1.0
jQuery(function($) {
  $(document).ready(function(){
       $('.widget .menu-artists-dropdown-container ul.menu').fadeOut();
       // Create the dropdown base
       $("<select />").appendTo(".widget .menu-artists-dropdown-container");
      
       // Create default option "Go to..."
       $("<option />", {
           "selected": "selected",
           "value"   : "",
           "text"    : "Select Artist"
       }).appendTo(".widget .menu-artists-dropdown-container select");

       // Populate dropdown with menu items
       $(".widget .menu-artists-dropdown-container a").each(function() {
           var el = $(this);
           $("<option />", {
           "value"   : el.attr("href"),
           "text"    : el.text()
       }).appendTo(".widget .menu-artists-dropdown-container select");
      });

      $(".widget .menu-artists-dropdown-container select").change(function() {
        window.location = $(this).find("option:selected").val();
    });
  }); // end document ready
});

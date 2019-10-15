jQuery('.user-edit-php h2:contains("About the user")').hide();
jQuery('.user-edit-php h2:contains("Account Management")').hide();
 jQuery('.user-edit-php .form-table tr th:contains("Other Roles")').closest('.form-table').hide();

jQuery('.user-new-php .form-table tr th:contains("Other Roles")').closest('.form-table').hide();
// jQuery('.user-new-php .form-table tr:contains("Send User Notification")').hide();
// jQuery('.user-new-php .form-table tr label:contains("Website")').closest('.form-field').hide();

// jquery for hide unwanted headdings of user profile page 
jQuery('.profile-php h2:contains("About the user")').hide();
jQuery('.profile-php h2:contains("Account Management")').hide();
jQuery('.profile-php h2:contains("About Yourself")').hide();

jQuery('.user-new-php #role option:contains("Agency Administrator")').remove();
jQuery('.user-new-php #role option:contains("Contributor")').remove();
jQuery('.user-new-php #role option:contains("Author")').remove();
jQuery('.user-new-php #role option:contains("Editor")').remove();
jQuery('.user-new-php #role option:contains("Subscriber")').remove();






jQuery('.agency_filter').on('change', function() {
  var aid=this.value ;
  window.location.replace("http://lockedsandbox.com/fellow/wp-admin/admin.php?page=subsciber&fagency_id="+aid);

});




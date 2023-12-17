
/**
 * @file
 * Plugin front-end JS.
 *
 * Created by: Anees
 * http://www.Anees.com/
 */
jQuery.noConflict();
jQuery(document).ready(function($) {
  // Parse the URL and extract the email parameter value
  var url = window.location.href;
  var email = getParameterByName('email', url);

  // Populate the 'user_login' input field with the email value
  $('#user_login').val(email);
});

// Function to retrieve URL parameter value by name
function getParameterByName(name, url) {
  name = name.replace(/[\[\]]/g, '\\$&');
  var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
  var results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

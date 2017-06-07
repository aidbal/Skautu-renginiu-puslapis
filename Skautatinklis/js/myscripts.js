function toggle() {
 if( document.getElementById("hidethis").style.display === 'none' ){
   document.getElementById("hidethis").style.display = 'table-row'; // set to table-row instead of an empty string
 }else{
   document.getElementById("hidethis").style.display = 'none';
 }
}
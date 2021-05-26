const navModal = document.querySelector(".main-menu")
const mainMenuIcon = document.querySelector(".main-menu-icon")
const modalBody = document.querySelector(".modal-body");
var menuOut = false
function toggleMenu(){
    if(menuOut){
        menuOut = false
        navModal.style.display = "flex"
    }else{
        navModal.style.display = "none"

        menuOut = true
    }
}


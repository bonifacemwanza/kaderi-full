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

const buyModal = document.getElementById("buyBook")
var buyModalOut = false
function buyBtn(){
    if(buyModalOut){
        buyModalOut = false
        buyModal.style.display = "none"
    }else{
        buyModal.style.display = "flex"

        buyModalOut = true
    }
}

const overviewModal = document.getElementById("overviewBook")
var overviewModalOut = false
function redeemBtn(){
    if(overviewModalOut){
        overviewModalOut = false
        overviewModal.style.display = "none"
    }else{
        overviewModal.style.display = "flex"

        overviewModalOut = true
    }
}
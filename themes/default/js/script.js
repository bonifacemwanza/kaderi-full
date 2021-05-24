const overviewTab = document.querySelector('.switch-overview')
const curriculumTab = document.querySelector('.switch-curriculum')
const instructorTab = document.querySelector('.switch-instructor')

const overviewCard = document.querySelector('.overview-container')
const curriculumCard = document.querySelector('.curriculum-container')
const instructorCard = document.querySelector('.instructor-container')

function overview(){
    overviewTab.classList.add("navbar-item-active")
    curriculumTab.classList.remove("navbar-item-active")
    instructorTab.classList.remove("navbar-item-active")

    overviewCard.style.display = 'block'
    curriculumCard.style.display = 'none'
    instructorCard.style.display = 'none'
}
function curriculum(){
    overviewTab.classList.remove("navbar-item-active")
    curriculumTab.classList.add("navbar-item-active")
    instructorTab.classList.remove("navbar-item-active")

    overviewCard.style.display = 'none'
    curriculumCard.style.display = 'block'
    instructorCard.style.display = 'none'
}
function instructor(){
    overviewTab.classList.remove("navbar-item-active")
    curriculumTab.classList.remove("navbar-item-active")
    instructorTab.classList.add("navbar-item-active")

    overviewCard.style.display = 'none'
    curriculumCard.style.display = 'none'
    instructorCard.style.display = 'block'
}

const toLoginCard = document.querySelector('.login-page')
const toRegisterCard = document.querySelector('.register-page')

function toRegister(){
    toLoginCard.style.display = 'none'
    toRegisterCard.style.display = 'block'
}

function toLogin(){
    toLoginCard.style.display = 'block'
    toRegisterCard.style.display = 'none'
}

const toNav1 = document.querySelector('.nav1')
const toNav2 = document.querySelector('.nav2')
const toNav3 = document.querySelector('.nav3')
const toNav4 = document.querySelector('.nav4')
const toNav5 = document.querySelector('.nav5')

function navsActive(){
    toNav1.classList.remove("nav-active")
    toNav2.classList.remove("nav-active")
    toNav3.classList.remove("nav-active")
    toNav4.classList.remove("nav-active")
    toNav5.classList.remove("nav-active")
}

function nav1(){
    navsActive()
    toNav1.classList.add("nav-active")
}
function nav2(){
    navsActive()
    toNav2.classList.add("nav-active")
}
function nav3(){
    navsActive()
    toNav3.classList.add("nav-active")
}
function nav4(){
    navsActive()
    toNav4.classList.add("nav-active")
}
function nav5(){
    navsActive()
    toNav5.classList.add("nav-active")
}

var sectionMenu = true

function toggleBtns(id){
    if(sectionMenu){
        document.querySelector(".btns-"+id).style.display = 'none'
        document.querySelector(".fas").classList.remove("fa-caret-up")
        document.querySelector(".fas").classList.add("fa-caret-down")
        sectionMenu = false

    }
    else{
        document.querySelector(".btns-"+id).style.display = 'block'
        document.querySelector(".fas").classList.remove("fa-caret-down")
        document.querySelector(".fas").classList.add("fa-caret-up")
        sectionMenu = true
    }
}

const navItems =  document.querySelector(".nav-items")
const toggleNavBar =  document.querySelector(".toggle-nav")
var navOut = false

function toggleNav(){
    if(navOut){
        navItems.style.right = '-100px'
        navOut = false
        toggleNavBar.innerHTML =  `<i class="fas fa-bars"></i>`
    }
    else{
        toggleNavBar.innerHTML =  `<i class="fas fa-ellipsis-v"></i>`
        navItems.style.right = 0
        navOut = true
    }
}

var toggleAudioIcon = false
const toggleAudioDiv =  document.querySelector(".play-icons")
function toggleAudio(){
    if(toggleAudioIcon){
        toggleAudioDiv.innerHTML =  `<i class="fas fa-play-circle"></i>`
        toggleAudioIcon = false
    }
    else{
        toggleAudioIcon = true
        toggleAudioDiv.innerHTML =  `<i class="fas fa-pause-circle"></i>`

    }
}



var toggleSideBarIcon = false
const toggleSideBarDiv =  document.querySelector(".preview-side-bar")
function toggleSideBar(){
    if(toggleSideBarIcon){
        toggleSideBarIcon = false
        toggleSideBarDiv.style.left = '-100%'
    }
    else{
        toggleSideBarIcon = true
        toggleSideBarDiv.style.left = '0'
    }
}
const answerModal = document.querySelector(".answer-modal")
function submitAnswer(){
    answerModal.style.display = 'flex'
}
function closeModal(){
    answerModal.style.display = 'none'
}

var showLessonToggle = false

const showLessonDiv = document.querySelector(".show-lesson-div")
const showLessonBtn = document.querySelector(".show-lesson-btn")
function showLesson(){
    if(showLessonToggle){
        showLessonDiv.style.display = 'none'
        showLessonBtn.innerHTML = `<span>Show Lesson</span><i class="fas fa-caret-down"></i>`
        showLessonToggle = false
    }
    else{
        showLessonDiv.style.display = 'flex'
        showLessonToggle = true
        showLessonBtn.innerHTML = `<span>Hide Lesson</span><i class="fas fa-caret-up"></i>`


    }
}
function quizOptionSelector(show){
    show.classList.add("active-answer")
    
}
function Child1(id){
    quizOptionSelector(document.getElementById("Child-"+id))
}

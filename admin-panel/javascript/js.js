function addStudent(){
    var element = document.querySelector('.add-student-modal');
    element.style.display = 'flex';
}
function addTeacher(){
    var element = document.querySelector('.add-teacher-modal');
    element.style.display = 'flex';
}
function addSubject(){
    var element = document.querySelector('.add-subject-modal');
    element.style.display = 'flex';
}
function addClass(){
    var element = document.querySelector('.add-class-modal');
    element.style.display = 'flex';
}
function cancelAddStudent(){
    var element = document.querySelector('.add-student-modal');
    element.style.display = 'none';
}
function cancelAddTeacher(){
    var element = document.querySelector('.add-teacher-modal');
    element.style.display = 'none';
}
function cancelAddSubject(){
    var element = document.querySelector('.add-subject-modal');
    element.style.display = 'none';
}
function cancelAddClass(){
    var element = document.querySelector('.add-class-modal');
    element.style.display = 'none';
}
function updateStudent(){
    // var element = document.querySelector('.student-update');
    // element.style.display = 'flex';
    console.log("hello")
}
tinymce.init({
    selector: '#jb_resp',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    },
    height: 160,
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks fullscreen',
        'media contextmenu paste'
    ],
    toolbar: 'bold underline italic | bullist numlist',
});

tinymce.init({
    selector: '#jb_qual',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    },
    height: 160,
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks fullscreen',
        'media contextmenu paste'
    ],
    toolbar: 'bold underline italic | bullist numlist',
});

tinymce.init({
    selector: '#jb_desc',
    setup: function (editor) {
        editor.on('change', function () {
            editor.save();
        });
    },
    height: 160,
    menubar: false,
    plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks fullscreen',
        'media contextmenu paste'
    ],
    toolbar: 'bold underline italic | bullist numlist | alignleft aligncenter alignright alignjustify | link',
});
   
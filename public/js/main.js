let fileInput = document.querySelector(".content__file_load__input");
let filePar = document.querySelector(".content__file_load__name");
let fileBtn = document.querySelector(".content__file_load__button");

fileInput.addEventListener('change',function(){
    let fileName = this.value.split('\\');
    filePar.innerHTML = fileName[fileName.length - 1]
    if(fileName !== ""){
        fileBtn.classList.remove("disabled")
    }
})
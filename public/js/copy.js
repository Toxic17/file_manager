let copyLinks = document.querySelectorAll('.copy-link');
copyLinks.forEach((elem)=>{
    elem.addEventListener('click',function(e){
        e.preventDefault();
        copyPageUrl(this.href);
        let alert = document.querySelector(".link_status");
        if(alert == null) {
            document.querySelector('.content').insertAdjacentHTML("beforeend", `
                <div class="alert alert-success link_status p-0 position-fixed" role="alert">
                    <div class="alert__container p-3">
                        <p class="mt-2 mb-0">
                        ` + `Link copied` + `
                        </p>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                    `);
        }
    })
})


async function copyPageUrl(text) {
    var copyTextarea = document.createElement("textarea");
    copyTextarea.style.position = "fixed";
    copyTextarea.style.opacity = "0";
    copyTextarea.textContent = text;

    document.body.appendChild(copyTextarea);
    copyTextarea.select();
    document.execCommand("copy");
    document.body.removeChild(copyTextarea);
}
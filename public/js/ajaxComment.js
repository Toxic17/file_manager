//Open all comment
let fileCommentLinks = document.querySelectorAll(".comment-link");
fileCommentLinks.forEach((elem)=>{
    elem.addEventListener('click',function(e){
        e.preventDefault();
        let url = elem.href;
        showComments(url);
    })
})

function showComments(url){
    let id =url.substr(32,40);
    let link = document.getElementById("file-" + id);
    let commentDiv = document.createElement("div");
    commentDiv.classList.add("file-"+id);
    fetch(url).then(response => response.json()).then(result => {
        if(link.dataset.status == "close") {
            link.dataset.status = "open";
            commentDiv.classList.add("comments");
            link.after(commentDiv);
            if (result.length !== 0) {
                result.forEach(function (data) {
                    commentDiv.insertAdjacentHTML("beforeend", `
                        <div class="comment mt-4">
                            <p class="text-secondary m-0">Date:2022-10-22</p>
                            <p>
                                ` + data['value'] + `
                            </p>
                            <hr>
                    </div>
                    `)
                })
            }
            commentDiv.insertAdjacentHTML("beforeend", `<form method="post" class="form-custom" action="/files/comments/add/` + id + `">
                       <label for="text_comment">Text comment</label>
                        <textarea class="form-control mt-3" id="text_comment" name="text_comment" rows="3"></textarea>
                        <input class="btn btn-success mt-3" value="Add comment" type="submit">
                    </form>`)
            link.innerHTML = "Close comments";
            let divAddComment = document.querySelector("div.file-"+id +"");
            let formAddComment = divAddComment.lastChild;
            formAddComment.addEventListener("submit",(form)=>{
                addComment(form,url);
            })
        }
        else{
            let commentDiv = document.querySelector(".file-"+id);
            commentDiv.remove();
            link.innerHTML = "Show all comments(" + result.length +")";
            link.dataset.status = "close";
        }
    });
}

//add Comment
function addComment(form,url){
    form.preventDefault();
    let request = new XMLHttpRequest();
    let link = form.srcElement.action;
    request.open("POST",link,true);
    let text = form.srcElement.querySelector("textarea").value;
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.send("comment_text="+text);
    request.onload = function(){
        if(request.readyState == 4 & request.status == 200){
            let json = JSON.parse(request.response);
            let status = json.status;
            if(status.error != undefined){
                let commentStatus = document.querySelector(".comment_status");
                if(commentStatus != undefined){
                    commentStatus.remove();
                }
                document.querySelector('.content').insertAdjacentHTML("beforeend",`
                <div class="alert alert-danger comment_status p-0 position-fixed" role="alert">
                    <div class="alert__container p-3">
                        <p class="mt-2 mb-0">
                        `+ status.error +`
                        </p>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                    `);
            }
            else{
                let commentStatus = document.querySelector(".comment_status");
                let comments = document.querySelectorAll(".comment");
                if(commentStatus != undefined){
                    commentStatus.remove();
                }
                //add Alert
                document.querySelector('.content').insertAdjacentHTML("beforeend",`
                <div class="alert alert-success comment_status p-0 position-fixed" role="alert">
                    <div class="alert__container p-3">
                        <p class="mt-2 mb-0">
                        `+ `Your comment added` +`
                        </p>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                    `);
                //Upload comments
                let date = new Date();
                let resultDate =date.getFullYear() + "-" + date.getMonth() + 1 + "-" + date.getDate() + " " + date.getHours() + ":"+ (date.getMinutes()<10?'0':'') + date.getMinutes() + ":" + date.getSeconds();
                let text = json.value;
                if(comments[0] != undefined) {
                    comments[0].insertAdjacentHTML("beforebegin", `
                    <div class="comment mt-4">
                        <p class="text-secondary m-0">Date:` + resultDate + `</p>
                        <p>
                            ` + text + `
                        </p>
                        <hr>
                    </div>
                    `);
                }
                else{
                    let id =url.substr(32,40);
                    let link = document.getElementById("file-" + id);
                    let commentDiv = document.createElement("div");

                    commentDiv.classList.add("file-"+id);
                    form.srcElement.before(commentDiv);
                    commentDiv.insertAdjacentHTML(`afterbegin`,`
                    <div class="comment mt-4">
                        <p class="text-secondary m-0">Date:` + resultDate + `</p>
                        <p>
                            ` + text + `
                        </p>
                        <hr>
                    </div>`)
                }
                form.srcElement.querySelector("textarea").value = "";
            }
        }
    }
}
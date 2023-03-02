const timeAnim = 800;
let header = document.querySelector('header');
let headerMobileBtn = document.querySelector('.header__mobile__btn');
let scrollWidth = window.innerWidth - document.body.clientWidth;
let statusMenu = "close";
let statusBtn = "available";
let scroll = 0;

		headerMobileBtn.addEventListener('click',function(){
			let desktopHeight = window.innerHeight;
			let desktopWidth = window.innerWidth;
			if(desktopWidth <= 768 || desktopHeight <= 400){
			if(statusMenu == "close" && statusBtn == "available"){
				scroll = window.pageYOffset;
				this.classList.add('active');
				header.classList.add('active');
				statusMenu = "open";
				statusBtn = "unavailable"
				setTimeout(()=>{
					document.body.style.paddingRight = scrollWidth + "px";
					headerMobileBtn.style.right = scrollWidth + "px";
					document.body.classList.add('lock');
					statusBtn = "available"
			},timeAnim)
		}
		else if(statusBtn == "available"){

				this.classList.remove('active');
				header.classList.remove('active');
				
				statusMenu = "close";
				statusBtn = "unavailable";
				document.body.classList.remove('lock');
				document.body.style.paddingRight = 0 + "px";
					headerMobileBtn.style.right = 0 + "px";
					window.scrollTo(0, scroll);
				setTimeout(()=>{
					statusBtn = "available"
			},timeAnim)
		}
		}})


//player
Object.defineProperty(HTMLMediaElement.prototype, 'playing', {
    get: function(){
        return !!(this.currentTime > 0 && !this.paused && !this.ended && this.readyState > 2);
    }
})

let playerBtn = document.querySelector('.story__video__icon');
let player = document.querySelector('.story__video__player');

playerBtn.addEventListener('click',function(){
	player.play();
	player.controls = "controls";
	this.classList.add('hide');

})


player.addEventListener('pause',()=>{
	if(!player.playing){
	player.controls = false;
	playerBtn.classList.remove('hide');
}
})
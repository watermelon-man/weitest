function ScrollText(content){
        this.Delay=10;
        this.Amount=1;
        this.Direction="up";
        this.Timeout=1000;
        this.ScrollContent=this.gid(content);
        this.ScrollContent.innerHTML += this.ScrollContent.innerHTML;
        this.ScrollContent.onmouseover = this.GetFunction(this,"Stop");
        this.ScrollContent.onmouseout = this.GetFunction(this,"Start");
    }

ScrollText.prototype.gid=function(element){
    return document.getElementById(element);
}
ScrollText.prototype.Stop=function(){
    clearTimeout(this.AutoScrollTimer);
    clearTimeout(this.ScrollTimer);
}
ScrollText.prototype.Start=function(){
    clearTimeout(this.AutoScrollTimer);
    this.AutoScrollTimer=setTimeout(this.GetFunction(this,"AutoScroll"),this.Timeout);
}

ScrollText.prototype.AutoScroll=function(){
    if(this.Direction=="up"){
        if(parseInt(this.ScrollContent.scrollTop)>=parseInt(this.ScrollContent.scrollHeight)/2){
            this.ScrollContent.scrollTop=0;
            clearTimeout(this.AutoScrollTimer);
            this.AutoScrollTimer = setTimeout(this.GetFunction(this,"AutoScroll"), this.Timeout);
            return;
        }
        this.ScrollContent.scrollTop += this.Amount;
    }else
    {
        if(parseInt(this.ScrollContent.scrollTop) <= 0)
        {
            this.ScrollContent.scrollTop = parseInt(this.ScrollContent.scrollHeight) / 2;
        }
        this.ScrollContent.scrollTop -= this.Amount;
    }
    if(parseInt(this.ScrollContent.scrollTop) % this.LineHeight != 0)
    {
        this.ScrollTimer = setTimeout(this.GetFunction(this,"AutoScroll"), this.Delay);
    }
    else
    {
        this.AutoScrollTimer = setTimeout(this.GetFunction(this,"AutoScroll"), this.Timeout);
    }
}

ScrollText.prototype.GetFunction=function(variable,method){
        return function()
        {
            variable[method]();
        }
    }
var scrollup = new ScrollText("textHeight");
scrollup.Start();

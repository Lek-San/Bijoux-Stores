function messageOff()
{
    setTimeout(
        function(){
            document.querySelector('.disparition').style.display = "none";
        },4000
    );
}

messageOff();
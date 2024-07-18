var affiche = 0;
var name = "";
var Mouse = {"x":0,"y":0};

// Demande de gestion de l'evenement à NetScape
if(navigator.appName.substring(0,3) == "Net") {
	document.captureEvents(Event.MOUSEMOVE);
}
 
// Gestion de l'evenement
var OnMouseMoveEventHandler=function() {}
var OnMouseMove = function (e)
{
   Mouse.x = (navigator.appName.substring(0,3) == "Net") ? e.pageX : event.x+document.body.scrollLeft;
   Mouse.y = (navigator.appName.substring(0,3) == "Net") ? e.pageY : event.y+document.body.scrollTop;
   if (Mouse.x < 0) {Mouse.x=0;}
   if (Mouse.y < 0) {Mouse.y=0;}
   OnMouseMoveEventHandler(e)
}
 
try {
   document.attachEvent("onmousemove", OnMouseMove, true);
}
catch (ex) {
   document.addEventListener("mousemove", OnMouseMove, true);
}

function getXhr()
{
	var xhr = null; 
	if(window.XMLHttpRequest) // Firefox et autres
		xhr = new XMLHttpRequest(); 
		else if(window.ActiveXObject) // Internet Explorer 
		{
			try
			{
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e)
			{
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		}
		else // XMLHttpRequest non supporté par le navigateur 
		{
			alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
			xhr = false; 
		}
	return xhr
}

function ajax(obj, evenement)
{
	if (affiche==0 || name!=obj.getAttribute("name"))
	{
		var xhr = getXhr()
		// On défini ce qu'on va faire quand on aura la réponse
		xhr.onreadystatechange = function()
		{
			// On ne fait quelque chose que si on a tout reçu et que le serveur est ok
			if(xhr.readyState == 4 && xhr.status == 200)
			{
				leselect=xhr.responseText;
				var div = document.getElementById('descriptionEvenement');
				div.innerHTML = leselect;
				div.style.display = 'block';
				var height = div.offsetHeight;
				if(navigator.appName == "Microsoft Internet Explorer"){
					div.style.top = (Mouse.y-height)+"px";
					div.style.left = (Mouse.x)+"px";
				}
				else
				{
					div.style.top = (Mouse.y-height-(document.getElementById('evenements').offsetTop))+"px";
					div.style.left = (Mouse.x-(document.getElementById('evenements').offsetLeft))+"px";
				}
				affiche=1;
			}
		}
		xhr.open("POST","script/ajaxCalendrier.php",true);
		xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
		name = obj.getAttribute("name");
		xhr.send("date="+name);
	}
	else
	{
		document.getElementById('descriptionEvenement').style.display = 'none';
		affiche=0;
	}
}
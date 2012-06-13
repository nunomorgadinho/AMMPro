/*
	Written by Jonathan Snook, http://www.snook.ca/jonathan
	Add-ons by Robert Nyman, http://www.robertnyman.com
*/
function getElementsByClassName(oElm, strTagName, strClassName){
	var arrElements = (strTagName == "*" && oElm.all)? oElm.all : oElm.getElementsByTagName(strTagName);
	var arrReturnElements = new Array();
	strClassName = strClassName.replace(/-/g, "\-");
	var oRegExp = new RegExp("(^|\s)" + strClassName + "(\s|$)");
	var oElement;
	for(var i=0; i<arrElements.length; i++){
		oElement = arrElements[i];
		if(oRegExp.test(oElement.className)){
			arrReturnElements.push(oElement);
		}
	}
	return (arrReturnElements)
}

// From Quirksmode - http://www.quirksmode.org/js/events_properties.html
// (slightly modified)
function GetTarget(Event){
	var Target;
	if (!Event) var Event = window.event;
	if (Event.target) Target = Event.target;
	else if (Event.srcElement) Target = Event.srcElement;
	if (Target.nodeType == 3) // defeat Safari bug
		Target = Target.parentNode;

  return Target;
}

// From Ajax Cookbook - http://ajaxcookbook.org/event-handling-memory-leaks/
// (Slightly modified.)
function Listen(instance, eventName, listener) {
  if (instance.addEventListener) {
    instance.addEventListener(eventName, listener, false);
  } else if (instance.attachEvent) {
    var f = listener;
    listener = function() {
      f(window.event);
    }
    instance.attachEvent("on" + eventName, listener);
  } else {
    throw new Error("Event registration not supported");
  }
}

if(!Array.indexOf){
  Array.prototype.indexOf = function(obj){
    for(var i=0; i<this.length; i++){
      if(this[i]==obj){
        return i;
      }
    }
    return -1;
  }
}




// Will be used by new images before they are uploaded
function RemoveIcon(Event){
	  var RemoveButton = GetTarget(Event);
	  var Icon = RemoveButton.parentNode;

	  // If the first icon is removed, set the next icon as first.
	  if(Icon.className == "First" && Icon.nextSibling)
	    Icon.nextSibling.className = "First";
	  
	  // Remove
	  Icon.parentNode.removeChild(RemoveButton.parentNode);
}

function RemoveIconAjax(Event, filename, postid){
  var RemoveButton = GetTarget(Event);
  var Icon = RemoveButton.parentNode;

  var agree=confirm("Are you sure you want to delete?");
  if (!agree)
  return;
  
  // If the first icon is removed, set the next icon as first.
  if(Icon.className == "First" && Icon.nextSibling)
    Icon.nextSibling.className = "First";
  
  var data = {
			action: 'my_special_action',
			whatever: filename,
			id: postid
		};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) {
		//alert('Got this from the server: ' + response);
	});

  // Remove
  Icon.parentNode.removeChild(RemoveButton.parentNode);
}


function GetForm(Field){
  var TheForm = Field.parentNode;
  while(TheForm.tagName.toLowerCase() != "form"){
    TheForm = TheForm.parentNode;
  }
  return TheForm;
}

function SetWideOrTall(Icon){
	 Icon.parentNode.className = (Icon.height/Icon.width > 0.75) ? "Tall" : "Wide"; // I define "Tall" as having an aspect ratio that's taller than 3/4. It's a pretty arbitrarily value, though.
}

function wwIconifiedOnChange(Event){

  // Get the field with the new file.
  var Field = GetTarget(Event);

  // Find the place where to put the file icon.
  var Container = getElementsByClassName(GetForm(Field), "ul", "wwIconified").pop();

  // Create the icon base node.
  var Base = document.createElement("li");
  if(!Container.hasChildNodes())
    Base.className = "First"; // Makes styling a bit easier.

  // Create the actual icon.
  var IconFrame = document.createElement("div");
  Base.appendChild(IconFrame);
  var ImageFileTypes = ["jpeg", "jpg", "gif", "png", "bmp", "tga", "tif", "tiff"];
  var Matches = /([^\/\\]*[\/\\])*([^\/\\]+)\.(\w+)/.exec(Field.value);
  var FileType = (Matches!=null ? Matches[3].toLowerCase() : "");
  if(ImageFileTypes.indexOf(FileType) >= 0){
    // Create a thumbnail of the local image.
    var Icon = document.createElement("img");
    IconFrame.appendChild(Icon);
    Listen(Icon, "load", function(){SetWideOrTall(Icon);});  // Icon.width is not available until it's loaded, but It's useful for the styling to know if the image is wide or tall.
    Icon.src = "file:///"+Field.value; // Must be done after we add the event listenerand put the icon into the DOM structure, or the onload event might not be triggered after we set the initial values.
  }else{
    // Let the styling take care of the icons for other file types.
    IconFrame.className = FileType;
  }

  // The file name.
  var FileName = document.createElement("p");
  var FileText = /([^\/\\]*[\/\\])*([^\/\\]+)/.exec(Field.value)[2];
  var TextLength = 40;
  if(FileText.length > (TextLength+2))
    FileText = FileText.substr(0, TextLength)+"...";
  FileName.innerHTML = FileText;
  Base.appendChild(FileName);

  // Add a "remove" button.
  var RemoveButton = document.createElement("button");
  RemoveButton.setAttribute("type", "button");
  RemoveButton.className = "RemoveButton";
  var Label = document.createElement("span");
  Label.innerHTML = "Remove";
  RemoveButton.appendChild(Label);
  Listen(RemoveButton, "click", RemoveIcon);
  Base.appendChild(RemoveButton);

  // Make a copy of the visible file field, sans the value.
  var BlankField = document.createElement("input");
  BlankField.type = "file";
  BlankField.name = Field.name;
  BlankField.id = Field.id;
  BlankField.className = Field.className;
  Listen(BlankField, "change", wwIconifiedOnChange);
  Field.parentNode.insertBefore(BlankField, Field);
  
  // Move the file upload field to the icon area, so the file can get uploaded. (You probably want to hide it with a "display: none;" in your stylesheet, since it makes no sense to have it visible.)
  Field.className = "File";
  Base.appendChild(Field);
//   Base.insertBefore(Field, RemoveButton);
  
  // Show it all to the world!
  Container.appendChild(Base);
}

function wwIconifyFileUploadInit(){
  // For all instances of the wwIconify file upload field.
  var Fields = getElementsByClassName(document, "input", "wwIconified");
  for(var i=0; i<Fields.length; i++){
    // Add the event listener.
    Listen(Fields[i], "change", wwIconifiedOnChange); // The main action.

    // Make sure there's a place to put the icons.
    var Container = getElementsByClassName(GetForm(Fields[i]), "ul", "wwIconified").pop();
    if(!Container){
      // Someone was too lazy to make a container for the icons, so let's create one now, and insert it just before the file-field.
      Container = document.createElement("ul");
      Container.className = "wwIconified";
      Fields[i].parentNode.insertBefore(Container, Fields[i]);
    }
  }
  
  // Hide all remove buttons
  var Fields = getElementsByClassName(document, "ul", "wwIconified");
//  alert("fields = " + Fields);
  for(var i=0; i<Fields.length; i++){

    var Buttons = getElementsByClassName(GetForm(Fields[i]), "button", "RemoveButton");
    for(var j=0; j<Buttons.length; j++){
    	button = Buttons[j];
    	//Listen(button, "onMouseOver", removeButtonOnOver);
//    	Listen(button, "onMouseOut", removeButtonOnOut);
    	button.style.visibility = "hidden";
    }

  }
}

function showRemoveButtons(Event)
{
	// Hide all remove buttons
	var Fields = getElementsByClassName(document, "ul", "wwIconified");

	for(var i=0; i<Fields.length; i++){

	    var Buttons = getElementsByClassName(GetForm(Fields[i]), "button", "RemoveButton");
	    for(var j=0; j<Buttons.length; j++){
	    	button = Buttons[j];
	    	button.style.visibility = "visible";
	    }

	}
}

function hideRemoveButtons(Event)
{
	// Hide all remove buttons
	var Fields = getElementsByClassName(document, "ul", "wwIconified");

	for(var i=0; i<Fields.length; i++){

	    var Buttons = getElementsByClassName(GetForm(Fields[i]), "button", "RemoveButton");
	    for(var j=0; j<Buttons.length; j++){
	    	button = Buttons[j];
	    	button.style.visibility = "hidden";
	    }

	}
}

Listen(window, "load", wwIconifyFileUploadInit);
var eventFields = ["coordinator", "speaker", "theme", "place", "hour", "day", "month", "year"];

function ToogleForm(sFormName)
{
	Effect.toggle(sFormName, 'appear');
	return false;
}

function AddPlayer()
{
	if($('name').value == "")
	{
		alert("Please enter valid name");
	}
	else if (!$('ip').value.match(/^\d+\.\d+\.\d+\.\d+$/))
	{
		alert("Please enter valid IP address");
	}
	else {
		var aData = new Array();
		aData['ip'] = $('ip').value;
		aData['name'] = $('name').value;
		aData['request'] = 'addplayer';
		aData['object'] = 'player';
		$('addPlayerForm').hide();
		SendPOSTRequestToServer(aData, "ws.php");
		
	}
}

function AddEvent()
{
	if($('coordinator').value == "")
	{
		alert("Please enter valid coordinator");
	}
	else if($('theme').value == "")
	{
		alert("Please enter valid theme");
	}
	else if($('place').value == "")
	{
		alert("Please enter valid place");
	}
	else if($('hour').value != "" && !$('hour').value.match(/^\d+(\:|\.)\d+$/))
	{
		alert("Please enter hour in xx:xx or xx.xx format, for example, 17:00 or 17.00");
	}
	else if($('day').value == ""  )
	{
		alert("Please enter valid date and time");
	}
	else
	{
		var aData = new Array();
		for(i = 0; i < eventFields.length; i++)
		{
			aData[eventFields[i]] = $(eventFields[i]).value;
		}
		aData['duration'] = 10;		
		aData['request'] = 'addevent';
		aData['object'] = 'event';
		$('addEventForm').hide();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function UpdateEventDateTime(sID)
{
	if($('hour_' + sID).value != "" && !$('hour_' + sID).value.match(/^\d+(\:|\.)\d+$/))
	{
		alert("Please enter hour in xx:xx or xx.xx format, for example, 17:00 or 17.00");
	}
	else if($('day_' + sID).value == "" )
	{
		alert("Please enter valid date and time");
	}
	else
	{
		var aData = new Array();

		aData['hour_' + sID] = $('hour_' + sID).value;
		aData['month_' + sID] = $('month_' + sID).value;
		aData['year_' + sID] = $('year_' + sID).value;
		aData['day_' + sID] = $('day_' + sID).value;

		aData['request'] = 'updatedateevent';
		aData['object'] = 'event';
		aData['id'] = sID;
		$('datetime_editor_' + sID).hide();
		$('loader_' + sID).show();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function AddVerse()
{
	if(!$('src').value.match(/^(.*) \d+/))
	{
		alert("Please enter the valid verse");
	}
	else if(!$('duration').value.match(/^\d+$/))
	{
		alert("Please enter valid duration in seconds");
	}
	else
	{
		var aData = new Array();
		aData['title'] = $('title').value;
		aData['duration'] = $('duration').value;
		aData['src'] = $('src').value;
		aData['request'] = 'addverse';
		aData['object'] = 'verse';
		$('addVerseForm').hide();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function AddMediaFile()
{
	if($('name').value == "" )
	{
		alert("Please enter the valid name");
	}
	else if($('media-file0').value == "")
	{
		alert("Please select media file");
	}
	else if(!$('media-file0').value.match(/\.mp4/i) && !$('media-file0').value.match(/\.jpg/i))
	{
		alert("Only mp4 or jpg files are accepted");
	}

	else
	{
		if($("duration").value == "") {
			$("duration").value = 10;
		}
		$('uploadForm').submit();
	}
}

function AddToPlaylist(iID, sName)
{
	var sHtml = "<li id=\"pl_" + iID + "\"><div class=\"media_pl\" >" + 
						"<div style=\"float: left\"><div class=\"media_name\">" + sName  + "</div></div>" +
						"<div style=\"float: right; padding-left: 10px; position: relative; top: -0.5em\"><span class=\"sort_icon\"><img src=\"img/drag.png\"></span></div>" +
						"<div id=\"deleteButton_" + iID + "\" style=\"float: right; padding-left: 10px; position: relative; top: -0.25em; \"><input class=\"button deleteButton\"  type=\"button\"  onclick=\"DeleteFromPlaylist(this)\" value=\"Remove\"></div>" + "<div style=\"clear: both\"></div>" +
					"</div></li>";

	//var sHtml = "<li>" + $('name_' + aParam[1]).innerHTML + " <span class=\"sort_icon\">move</span></li>";
	$('organizer').innerHTML += sHtml;

	Sortable.create("organizer", {
		handles:$$('#organizer .sort_icon'),
		onUpdate: function () 
		{
			UpdatePlaylist();
		}
	});
	

	UpdatePlaylist();
}

function DeleteEvent(iID)
{
	var ans = confirm("Are you sure you want to delete this event?");
	if(ans)
	{
		var aData = new Array();
		aData['id'] = iID;		
		aData['request'] = 'deleteevent';
		aData['object'] = 'event';
		$('deleteButton_' + iID).hide();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function DeleteVerse(iID)
{
	var ans = confirm("Are you sure you want to delete this verse?");
	if(ans)
	{
		var aData = new Array();
		aData['id'] = iID;		
		aData['request'] = 'deleteverse';
		aData['object'] = 'verse';
		$('deleteButton_' + iID).hide();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function DeleteVerse(iID)
{
	var ans = confirm("Are you sure you want to delete this verse?");
	if(ans)
	{
		var aData = new Array();
		aData['id'] = iID;		
		aData['request'] = 'deleteverse';
		aData['object'] = 'verse';
		$('deleteButton_' + iID).hide();
		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function DeleteMediaFile(iID)
{
	var ans = confirm("Are you sure you want to delete this media file?");
	if(ans)
	{
		var aData = new Array();
		aData['id'] = iID;		
		aData['request'] = 'deletemedia';
		aData['object'] = 'media';
		$('deleteButton_' + iID).hide();

		SendPOSTRequestToServer(aData, "ws.php");
	}
}

function ShowDatetimeEditor(sID)
{
	$('datetime_' + sID).hide();
	$('datetime_editor_' + sID).show();
}

function Cancel(sID)
{
	$('datetime_' + sID).show();
	$('datetime_editor_' + sID).hide();
}



function RestartPlayer(iID)
{
	var aData = new Array();
	aData['id'] = iID;		
	aData['request'] = 'remotecommand';
	aData['object'] = 'player';
	aData['cmd'] = "restart";

	SendPOSTRequestToServer(aData, "ws.php");
}

function RebootMachine(iID)
{
	var aData = new Array();
	aData['id'] = iID;		
	aData['request'] = 'remotecommand';
	aData['object'] = 'player';
	aData['cmd'] = "reboot";

	SendPOSTRequestToServer(aData, "ws.php");
}

function ShutdownMachine(iID)
{
	var aData = new Array();
	aData['id'] = iID;		
	aData['request'] = 'remotecommand';
	aData['object'] = 'player';
	aData['cmd'] = "shutdown";

	SendPOSTRequestToServer(aData, "ws.php");
}

function DeleteFromPlaylist(obj)
{
	$('organizer').removeChild(obj.parentNode.parentNode.parentNode);
	UpdatePlaylist();
}

function Login()
{
	if($('username').value == "")
	{
		alert("Please enter your username");
	}
	else if($('password').value == "")
	{
		alert("Please enter your password");
	}
	else
	{
		$('loginForm').submit();
	}
}

// used for making ajax request
var  xmlHttp;

/* 
=========================================================================================
	Helper methods
=========================================================================================
*/

function encodeFormData(aData)
{
	var pairs = [];
	var regexp = /%20/g;
	for( var name in aData )
	{
		if(aData.hasOwnProperty(name))
		{
			var value = aData[name].toString();
			var pair = encodeURIComponent(name).replace(regexp, "+") + '=' + 
						encodeURIComponent(value).replace(regexp, " ");
			pairs.push(pair);
		}
	}

	return pairs.join('&');
}

/* 
=========================================================================================
	AJAX Helper
=========================================================================================
*/

function CreateRequest()
{
	if(window.ActiveXObject)
	{
 		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
 	}
	else if(window.XMLHttpRequest)
 	{
		xmlHttp = new XMLHttpRequest();
	}
}

function SendPOSTRequestToServer(aData, sURL)
{
    // Create a http request
	CreateRequest();
	xmlHttp.open("POST", sURL, true);
	xmlHttp.onreadystatechange = OnPostStateChange;
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.send(encodeFormData(aData));
	$('loader').show();
}

function SendGETRequestToServer(aData, sURL)
{
    // Create a http request
	CreateRequest();
	sURL = (aData == null) ? sURL : sURL + "?" + encodeFormData(aData)
	xmlHttp.open("GET", sURL , true);
	xmlHttp.setRequestHeader("Access-Control-Allow-Origin", "*");
	xmlHttp.onreadystatechange = OnGetStateChange;
	xmlHttp.send(null);
}

function UpdatePlaylist()
{
	var sSerialized = Sortable.serialize('organizer');
	var sData = 'object=playlist&request=updateplaylist&param=content&player_id=' + $('player_id').value + '&type=' + $('playlist_type').value + "&" + sSerialized;
	
	new Ajax.Request("ws.php",{
		method: "post",
		parameters: sData,
		onSuccess: function(transport) {
			try
			{
				var oResp = eval('(' + xmlHttp.responseText + ')');		
			}
			catch (err)
			{
				alert("Error: " + xmlHttp.responseText);
			}
		}
	});
}

function OnPostStateChange()                                                    
{                                                                               
	if(xmlHttp.readyState == 4)                                             
	{                          
		$('loader').hide();
		try
		{
			var oResp = eval('(' + xmlHttp.responseText + ')');
			if(oResp.request == 'addplayer')
			{
				$('playerContainer').innerHTML += oResp.data;
				$('ip').value = $('name').value = "";
			}
			else if(oResp.request == 'addevent')
			{
				$('eventContainer').innerHTML += oResp.data;
				for(i = 0; i < eventFields.length; i++)
				{
					$(eventFields[i]).value = "";
				}
			}
			else if (oResp.request == 'deleteevent')
			{
				$('eventContainer').removeChild($('event_' + oResp.id));
			}
			else if(oResp.request == 'addverse')
			{
				$('verseContainer').innerHTML += oResp.data;
				$('duration').value = $('src').value = "";			
			}
			else if(oResp.request == 'deleteverse')
			{
				$('verseContainer').removeChild($('verse_' + oResp.id));
			}
			else if(oResp.request == 'deletemedia')
			{
				$('media_asset').removeChild($('media_' + oResp.id));
				
				for(;;)
				{
					try{
						$('organizer').removeChild($('pl_' + oResp.id));
					
					} catch (err) { break };
				}
				
				UpdatePlaylist();
			}
			else if(oResp.request == 'deleteplaylist')
			{

			}
			else if(oResp.request == 'remotecommand')
			{
				var aData = oResp.data.split('|');
				if(aData[1] == 0)
				{
					alert(aData[2]);
				}
			}
			else if(oResp.request == 'updatedateevent')
			{
				$('datetime_col_' + oResp.id).innerHTML = oResp.data;
			}
		}
		catch (err)
		{
			alert("Error OnPostStateChange: " + xmlHttp.responseText);
		}
		
		
	}
}              
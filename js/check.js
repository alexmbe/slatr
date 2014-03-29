// Declaring valid date character, minimum year and maximum year
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strDay=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("Format datuma mora biti : dd/mm/yyyy")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Uneli ste nepostojeci mesec")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Uneli ste nepostojeci datum")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Godina mora imati cetti cifre izmedju "+minYear+" i "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Unesite tacan datum")
		return false
	}
return true
}

function ValidateDate(formField){
	if (isDate(formField.value)==false){
		formField.focus()
		return false
	}
    return true
 }
 
 
 
 
 
function isEmailAddr(email)
{
  var result = false;
  var theStr = new String(email);
  var index = theStr.indexOf("@");
  if (index > 0)
  {
    var pindex = theStr.indexOf(".",index);
    if ((pindex > index+1) && (theStr.length > pindex+1))
  result = true;
  }
  return result;
}
function validRequired(formField,fieldLabel)
{
  var result = true;
  if (formField.value == "")
  {
    alert('You must enter "' + fieldLabel +'"');
    formField.focus();
    result = false;
  } 
  return result;
}

function checkRadio(formField,fieldLabel)
{
	for (i=0;i<formField.length;i++)
	{
		if (formField[i].checked)
		{
			return true
		}
	}
	alert('You must choose "' + fieldLabel +'"');
    formField[0].focus();
	return false
}

function validEmail(formField,fieldLabel,required)
{
  var result = true;
  if (required && !validRequired(formField,fieldLabel))
    result = false;
  if (result && ((formField.value.length < 3) || !isEmailAddr(formField.value)) )
  {
    alert("Please enter a complete email address in the form: yourname@yourdomain.com");
    formField.focus();
    result = false;
  }
  return result;
}

function isNum(theForm,fieldLabel)
{
    //alert('Please enter a value for the "' + fieldLabel +'" field.');    
	// Return false if characters are not '0-9' or '.' .
    if  (theForm.value.length == 0) return true;   
	for (var i = 0; i < theForm.value.length; i++)
        {
                var ch = theForm.value.substring(i, i + 1);
                if ((ch < "0" || "9" < ch) && ch != '.' && ch != '-')
                {		
                		alert('Please enter a numeric value for the "' + fieldLabel +'" field.');
                        theForm.focus();
                		return false;
                }
        }
        return true;
}
function isLength(theForm,fieldLabel,maxLength)
{
    //alert('Please enter a value for the "' + fieldLabel +'" field.');    
	// Return false if characters are not '0-9' or '.' .
    if  (theForm.value.length != maxLength){
		alert('Please enter ' + maxLength +' digits for"' + fieldLabel +'" field.');
        theForm.focus();
		 return false;
	}   
    return true;
}

function isMoney(theForm,fieldLabel)
{
    //alert('Please enter a value for the "' + fieldLabel +'" field.');    
	// Return false if characters are not '0-9' or '.' .

    if  (theForm.value.length == 0){
    	alert(fieldLabel);
        theForm.focus();                    
        return false;
    }
    var value = theForm.value;
    var oRegExp = /^\$?\-?([1-9]{1}[0-9]{0,2}(\,[0-9]{3})*(\.[0-9]{0,2})?|[1-9]{1}\d*(\.[0-9]{0,2})?|0(\.[0-9]{0,2})?|(\.[0-9]{1,2})?)$/;            		
    if (!oRegExp.test(value)){
		alert(fieldLabel);
        theForm.focus();                    
        return false;
	}
        return true;
}

function isequal(first, second, label)
{
	var result = false;
	if (first.value == second.value) result = true
	else {
		result = false;
		alert('Your data in "' + label +'" fields do not match.');
	 	first.focus();
	}
	return result;
}


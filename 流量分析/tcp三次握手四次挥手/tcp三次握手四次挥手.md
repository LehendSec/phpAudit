# TCP 协议三次握手过程

```
1. 当主机A想同主机B建立连接，主机A会发送SYN的数据包给主机B

2. 主机B接收到报文后，同意与主机A建立连接，会发送SYN、ACK数据包给主机A

3. 主机A接收到主机B发送过来的报文后，会发送ACK给主机B，建立连接完成，传输数据。
```



![](./tcpImage\wps56.jpg) 

# TCP 协议—四次挥手过程

```
1. 当主机A的应用程序通知TCP数据已经发送完毕时，TCP向主机B发送一个带有FIN的报文段

2. 主机B收到这个FIN报文段，并不立即用FIN报文段回复主机A，而是向主机A发送一个确认序号ack=x+1，同时通知自己的应用程序，对方要求关闭连接（先发ack是防止主机A重复发送FIN报文）。

3. 主机B发送完ack确认报文后，主机B的应用程序通知TCP我要关闭连接，TCP接到通知后会向主机A发送一个带有FIN的报文段，

4. 主机A收到这个FIN报文段，向主机B发送一个ack确认报文，表示连接彻底释放。
```

![](./tcpImage\wps57.jpg) 
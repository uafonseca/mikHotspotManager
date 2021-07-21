// jQuery(function(){
//     $.ajax({
//         url: Routing.generate('routerOs-trafic'),
//         type:'POST',
//         success:(response) => {
//             let bytes = response[0].data[0];   
//             console.log(bytes)                       
//             let sizes = ['bps', 'kbps', 'Mbps', 'Gbps', 'Tbps'];
//             if (bytes == 0) return '0 bps';
//             let i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
//             console.log(parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) );
//         }
//     });
// })
// function bytesToSize(bytes) {
//     var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
//     if (bytes == 0) return '0 Byte';
//     var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
//     return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
//  }
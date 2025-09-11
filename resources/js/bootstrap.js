import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.io = io;

window.Echo = new Echo({
    broadcaster: "socket.io",
    host: window.location.hostname + ":6001", // echo-server port
});

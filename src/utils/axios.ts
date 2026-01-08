import axios from "axios";
import Cookies from "js-cookie";
import { toast } from "react-toastify";

import { store } from "../store/Store";
import { setAccessToken, setUserDetails } from "../store/reducers/commonSlice";

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
});

instance.defaults.headers.common["Content-Type"] = "application/json";

instance.interceptors.request.use(
  (request) => {
    const token = Cookies.get("accesstoken_v1");
    request.headers.setAuthorization(token);

    return request;
  },
  (error) => {
    return Promise.reject(error);
  }
);

instance.interceptors.response.use(
  (response) => {
    return response;
  },
  (error) => {
    if (error?.response?.status === 401) {
      toast.error(error?.response?.data?.message);

      Cookies.remove("admin_v1", { path: "/" });
      Cookies.remove("accesstoken_v1", { path: "/" });

      store.dispatch(setAccessToken(null));
      store.dispatch(setUserDetails(null));

      window.location.assign("/");
      throw "Unauthorized Access";
    }
    return Promise.reject(error);
  }
);

export default instance;

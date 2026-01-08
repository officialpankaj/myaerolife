import { toast } from "react-toastify";

export default function showServerError(error) {
  if (error?.response?.status === 500) {
    toast.error(
      "Something went wrong, please try again. If the problem persists try contacting the site administrator.",
    );
    throw "API Server not responding";
  }
}

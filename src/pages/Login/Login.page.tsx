import { ErrorMessage, Field, Form, Formik } from "formik";
import { Base64 } from "js-base64";
import Cookies from "js-cookie";
import { useEffect, useRef, useState } from "react";
import { useDispatch } from "react-redux";
import { useLocation, useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import * as Yup from "yup";
import logo from "../../assets/logo-full.png";
import LoaderSpinner from "../../components/LoaderSpinner/LoaderSpinner.component";
import { UserRole } from "../../constants/common.constants";
import {
  setUserDetails,
  updateIsAuthenticated,
} from "../../store/reducers/commonSlice";
import Axios from "../../utils/axios";
import showServerError from "../../utils/showServerError";
import Header from "../../components/Header/Header.component";

const validationSchema = Yup.object({
  employee_code: Yup.string().required("Employee Code is required"),
  password: Yup.string().required("Password is required"),
  rememberme: Yup.boolean(),
});

const Login = () => {
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const location = useLocation();
  const buttonRef = useRef(null);
  const [isloading, setIsLoading] = useState(false);

  useEffect(() => {
    // Clear all existing data
    localStorage.clear();
  }, []);

  interface LoginFormValues {
    employee_code: string;
    password: string;
    rememberme: boolean;
  }

  interface LoginResponse {
    code: number;
    status: string;
    userdata: {
      id: string;
      name: string;
      email: string;
      role: string;
    };
    accesstoken: string;
  }

  interface AxiosErrorResponse {
    response?: {
      data?: {
        code?: number;
        message?: string;
      };
    };
    message?: string;
  }

  function handleSubmit(values: LoginFormValues): void {
    setIsLoading(true);
    Axios({
      url: "admin/login",
      method: "POST",
      data: {
        employee_code: values.employee_code,
        password: values.password,
        rememberme: values.rememberme,
      },
    })
      .then(({ data }: { data: LoginResponse }) => {
        if (data?.code === 200 && data?.status === "success") {
          const expiry = new Date();
          expiry.setHours(expiry.getHours() + 4);
          Cookies.set(
            "admin_v1",
            Base64.encode(JSON.stringify(data?.userdata)),
            {
              path: "/",
              expires: values.rememberme ? 1 : expiry,
            },
          );
          Cookies.set("accesstoken_v1", data?.accesstoken, {
            path: "/",
            expires: values.rememberme ? 1 : expiry,
          });
          dispatch(setUserDetails(data?.userdata));
          dispatch(updateIsAuthenticated(true));
          navigate("/dashboard");
        }
      })
      .catch((error: AxiosErrorResponse) => {
        console.log(error);
        showServerError(error);

        toast.error(error?.message);
      })
      .finally((): void => {
        setIsLoading(false);
      });
  }

  console.log("location", location);

  return (
    <div className="flex h-screen w-screen flex-col overflow-hidden">
      <Header />

      <div className="grid w-full flex-1 grid-cols-12">
        <div className="col-span-12 hidden items-center justify-center py-6 md:col-span-5 md:flex">
          <img src={logo} className="w-4/12 md:w-3/5" alt="company logo" />
        </div>
        <div className="col-span-12 flex items-center justify-center md:col-span-7">
          <div className="bg-secondary text-secondaryV2 flex h-full w-full flex-col items-center justify-center">
            <div className="w-10/12 rounded-sm bg-white px-8 py-8 shadow md:w-7/12 md:px-16 md:py-16">
              <h3 className="text-2xl font-bold">
                Sign in to your{" "}
                <i>
                  {location.state?.role === UserRole.MANAGER
                    ? "Manager"
                    : "Hero"}{" "}
                </i>{" "}
                account
              </h3>
              <Formik
                initialValues={{
                  employee_code: "",
                  password: "",
                  rememberme: false,
                }}
                validationSchema={validationSchema}
                onSubmit={handleSubmit}
              >
                {({ values, isValid }) => (
                  <Form className="py-5">
                    <div className="mb-2 flex flex-col gap-1.5">
                      <label
                        htmlFor="usernameField"
                        className="text-ssm font-semibold"
                      >
                        Employee ID
                      </label>
                      <Field
                        name="employee_code"
                        type="number"
                        className="text-secondaryV2 rounded-sm border border-gray-300 px-4 py-2.5 text-sm font-medium"
                        id="usernameField"
                        aria-describedby="usernameHelp"
                        autoFocus
                      />
                      <ErrorMessage
                        name="employee_code"
                        component="div"
                        className="text-xs text-red-500"
                      />
                      <div id="usernameHelp" className="text-xs">
                        Employee is the unique code associted with your records.
                      </div>
                    </div>
                    <div className="mb-2 flex flex-col gap-1.5">
                      <label
                        htmlFor="passwordField"
                        className="text-ssm font-semibold"
                      >
                        Password
                      </label>
                      <Field
                        name="password"
                        type="password"
                        className="text-secondaryV2 rounded-sm border border-gray-300 px-4 py-2.5 text-sm font-medium"
                        id="passwordField"
                      />
                      <ErrorMessage
                        name="password"
                        component="div"
                        className="text-xs text-red-500"
                      />
                      <div id="passwordHelp" className="text-xs">
                        enter the password associated with your account
                      </div>
                    </div>

                    <div className="mb-4 flex items-center gap-1.5">
                      <Field
                        name="rememberme"
                        type="checkbox"
                        className=""
                        id="rememberme"
                      />
                      <label
                        className="text-ssm font-semibold"
                        htmlFor="rememberme"
                      >
                        Stay signed in for a day
                      </label>
                    </div>
                    <button
                      type="submit"
                      className="bg-primary disabled:bg-primary/50 flex w-full cursor-pointer items-center justify-center rounded-sm py-3.5 text-sm font-semibold text-white"
                      disabled={
                        !isValid ||
                        values.employee_code === "" ||
                        values.password === ""
                      }
                      ref={buttonRef}
                    >
                      {!isloading && "Login"}
                      {isloading && <LoaderSpinner />}
                    </button>
                  </Form>
                )}
              </Formik>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Login;

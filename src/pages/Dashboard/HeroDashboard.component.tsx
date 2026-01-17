import { useEffect, useState } from "react";
import { Formik, Form, Field, ErrorMessage } from "formik";
import * as Yup from "yup";
import Axios from "../../utils/axios";
import Select from "react-select";
import Header from "../../components/Header/Header.component";
import { toast } from "react-toastify";
import successIcon from "../../assets/success-icon.svg";
import { LunchStatus, LunchStatusName } from "../../constants/common.constants";

const validationSchema = Yup.object({
  doctor_code: Yup.string().required("Doctor is required"),
  chemist_code: Yup.string().required("Chemist is required"),
  launch_status: Yup.string().required("Launch Status is required"),
  quantity: Yup.number()
    .typeError("Quantity must be a number")
    .required("Quantity is required")
    .max(9999, "Maximum is 9999")
    .min(1, "Minimum is 1"),
});

interface IDoctor {
  doctor_code: string;
}

interface IChemist {
  chemist_code: string;
}

const HeroDashboard = () => {
  const [doctorList, setDoctorList] = useState<IDoctor[]>([]);
  const [chemistList, setChemistList] = useState<IChemist[]>([]);
  const [loading, setLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);

  const fetchDoctorList = () => {
    Axios({ url: "/admin/doctors", method: "GET" })
      .then(({ data }) => setDoctorList(data?.data || []))
      .catch(() => setDoctorList([]));
  };

  const fetchChemistList = () => {
    Axios({ url: "/admin/chemists", method: "GET" })
      .then(({ data }) => setChemistList(data?.data || []))
      .catch(() => setChemistList([]));
  };

  useEffect(() => {
    fetchDoctorList();
    fetchChemistList();
  }, []);

  const doctorOptions = doctorList.map((doc) => ({
    value: doc?.doctor_code,
    label: doc?.doctor_code,
  }));

  const chemistOptions = chemistList.map((chem) => ({
    value: chem?.chemist_code,
    label: chem?.chemist_code,
  }));

  const lunchStatusOptions = Object.values(LunchStatus).map((status) => ({
    value: status,
    label: LunchStatusName[status],
  }));

  interface IScanFormValues {
    doctor_code: string;
    chemist_code: string;
    quantity: number | string;
    launch_status: LunchStatus | "";
  }

  interface IFormikHelpers {
    setSubmitting: (isSubmitting: boolean) => void;
    resetForm: () => void;
  }

  const handleSubmit = (
    values: IScanFormValues,
    { setSubmitting, resetForm }: IFormikHelpers,
  ) => {
    setLoading(true);
    Axios({
      url: "/admin/register-scan",
      method: "POST",
      data: values,
    })
      .then(() => {
        setShowModal(true);
        resetForm();
      })
      .catch((error: unknown) => {
        toast.error("Failed to add scan details");
        console.log("error: ", error);
      })
      .finally(() => {
        setLoading(false);
        setSubmitting(false);
      });
  };

  return (
    <div className="relative flex min-h-screen w-full flex-col items-center bg-gray-50">
      <Header />
      <div className="mt-20 flex w-11/12 max-w-md flex-col items-center rounded-2xl border border-gray-100 bg-white p-8 shadow-xl md:w-full">
        <h2 className="mb-6 text-center text-2xl font-semibold tracking-tight text-gray-800">
          Welcome!
          <br />
          Aerolife Mini Hero
        </h2>
        <Formik
          initialValues={{
            doctor_code: "",
            chemist_code: "",
            quantity: "",
            launch_status: "",
          }}
          validationSchema={validationSchema}
          onSubmit={handleSubmit}
        >
          {({ isSubmitting, setFieldValue, values, errors, touched }) => (
            <Form className="w-full space-y-6">
              <div className="flex flex-col gap-1">
                <label
                  htmlFor="doctor_code"
                  className="block text-sm font-semibold text-gray-700"
                >
                  Doctor ID
                </label>
                <Select
                  id="doctor_code"
                  name="doctor_code"
                  options={doctorOptions}
                  value={
                    doctorOptions.find(
                      (opt) => opt.value === values.doctor_code,
                    ) || null
                  }
                  onChange={(option) =>
                    setFieldValue("doctor_code", option ? option.value : "")
                  }
                  placeholder="Select Doctor"
                  isClearable
                  classNames={{
                    control: () =>
                      "!rounded-lg !border-gray-200 !shadow-none text-ssm font-medium text-black",
                    menuList: () => "font-semibold text-ssm text-black",
                    singleValue: () => "font-medium text-ssm text-black",
                  }}
                />
                {touched.doctor_code && errors.doctor_code && (
                  <div className="mt-1 text-xs text-red-500">
                    {errors.doctor_code}
                  </div>
                )}
              </div>
              <div className="flex flex-col gap-1">
                <label
                  htmlFor="chemist_code"
                  className="mb-1 block text-sm font-semibold text-gray-700"
                >
                  Chemist ID
                </label>
                <Select
                  id="chemist_code"
                  name="chemist_code"
                  options={chemistOptions}
                  value={
                    chemistOptions.find(
                      (opt) => opt.value === values.chemist_code,
                    ) || null
                  }
                  onChange={(option) =>
                    setFieldValue("chemist_code", option ? option.value : "")
                  }
                  placeholder="Select Chemist"
                  isClearable
                  classNames={{
                    control: () =>
                      "!rounded-lg !border-gray-200 !shadow-none text-ssm font-medium text-black",
                    menuList: () => "font-semibold text-ssm text-black",
                    singleValue: () => "font-medium text-ssm text-black",
                  }}
                />
                {touched.chemist_code && errors.chemist_code && (
                  <div className="mt-1 text-xs text-red-500">
                    {errors.chemist_code}
                  </div>
                )}
              </div>
              <div className="flex flex-col gap-1">
                <label
                  htmlFor="quantity"
                  className="mb-1 block text-sm font-semibold text-gray-700"
                >
                  POB Quantity collected today
                </label>
                <Field
                  name="quantity"
                  type="number"
                  className="focus:ring-primaryv2 w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 focus:ring-2 focus:outline-none"
                  max={9999}
                  min={1}
                />
                <ErrorMessage
                  name="quantity"
                  component="div"
                  className="mt-1 text-xs text-red-500"
                />
              </div>
              <div className="flex flex-col gap-1">
                <label
                  htmlFor="launch_status"
                  className="mb-1 block text-sm font-semibold text-gray-700"
                >
                  Lunch Status
                </label>
                <Select
                  id="launch_status"
                  name="launch_status"
                  options={lunchStatusOptions}
                  value={
                    lunchStatusOptions.find(
                      (opt) => opt.value === values.launch_status,
                    ) || null
                  }
                  onChange={(option) =>
                    setFieldValue("launch_status", option ? option.value : "")
                  }
                  placeholder="Select Launch Status"
                  isClearable
                  classNames={{
                    control: () =>
                      "!rounded-lg !border-gray-200 !shadow-none text-ssm font-medium text-black",
                    menuList: () => "font-semibold text-ssm text-black",
                    singleValue: () => "font-medium text-ssm text-black",
                  }}
                />
                {touched.launch_status && errors.launch_status && (
                  <div className="mt-1 text-xs text-red-500">
                    {errors.launch_status}
                  </div>
                )}
              </div>
              <button
                type="submit"
                className="bg-primaryv2 hover:bg-primaryv7 disabled:bg-primary/40 w-full rounded-lg py-2 font-medium text-white transition-colors duration-150"
                disabled={isSubmitting || loading}
              >
                {loading ? "Submitting..." : "Submit"}
              </button>
            </Form>
          )}
        </Formik>
      </div>

      {/* Confirmation Modal */}
      {showModal && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
          <div className="flex w-11/12 max-w-md flex-col items-center gap-3 rounded-2xl border border-gray-100 bg-white p-8 text-center shadow-2xl md:w-full">
            <div className="mb-2 flex items-center justify-center rounded-full bg-green-100 p-2">
              <img src={successIcon} className="w-16" alt="Success" />
            </div>
            <h3 className="text-base font-semibold text-gray-800">
              Scan details added successfully!
            </h3>
            <button
              className="bg-primaryv2 hover:bg-primaryv7 mt-2 rounded-lg px-5 py-1.5 text-sm font-medium text-white transition-colors"
              onClick={() => setShowModal(false)}
            >
              OK
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default HeroDashboard;

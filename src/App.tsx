import { RouterProvider, createBrowserRouter } from "react-router-dom";
import Login from "./pages/Login/Login.page";
import Home from "./pages/Home/Home.page";
import { Provider } from "react-redux";
import { PersistGate } from "redux-persist/integration/react";
import LoaderSpinner from "./components/LoaderSpinner/LoaderSpinner.component";
import { persistor, store } from "./store/Store";
import Dashboard from "./pages/Dashboard/Dashboard.page";
import { ToastContainer } from "react-toastify";

const App = () => {
  const router = createBrowserRouter([
    {
      path: "/",
      element: <Home />,
    },
    {
      path: "/login",
      element: <Login />,
    },
    {
      path: "/dashboard",
      element: <Dashboard />,
    },
  ]);

  return (
    <Provider store={store}>
      <PersistGate loading={<LoaderSpinner />} persistor={persistor}>
        <RouterProvider router={router} />
        <ToastContainer />
      </PersistGate>
    </Provider>
  );
};

export default App;

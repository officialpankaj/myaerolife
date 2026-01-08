import { useSelector } from "react-redux";
import { UserRole } from "../../constants/common.constants";
import HeroDashboard from "./HeroDashboard.component";
import ManagerDashboard from "./ManagerDashboard.component";

const Dashboard = () => {
  const { userDetails } = useSelector((state) => state.common);

  console.log("userDetails", userDetails);

  return (
    <div>{userDetails?.role === UserRole.HERO ? <HeroDashboard /> : <ManagerDashboard />}</div>
  );
};

export default Dashboard;

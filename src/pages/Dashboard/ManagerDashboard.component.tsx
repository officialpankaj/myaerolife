import { useEffect, useState } from "react";
import Header from "../../components/Header/Header.component";
import Axios from "../../utils/axios";
import { downloadExcelFile } from "../../utils/downloadExcel.util";

import { ComposableMap, Geographies, Geography } from "react-simple-maps";
import IndiaTopoJson from "../../maps/IND.json";

const ManagerDashboard = () => {
  const [stateData, setStateData] = useState<
    Record<string, { pob_count: number; doctor_count: number }>
  >({});
  const [totalStats, setTotalStats] = useState<{
    pob: number;
    doctors: number;
  }>({ pob: 0, doctors: 0 });
  const [activeState, setActiveState] = useState<{
    state: string;
    pob_count: number;
    doctor_count: number;
  } | null>(null);
  const [loading, setLoading] = useState(false);

  const fetchData = () => {
    setLoading(true);
    Axios({ url: "/admin/all-scans-by-state", method: "GET" })
      .then(({ data }) => {
        const structuredData = data?.data?.reduce((prev, curr) => {
          prev[curr?.state] = {
            pob_count: curr?.pob_count || 0,
            doctor_count: curr?.doctor_count || 0,
          };
          return prev;
        }, {});
        setStateData(structuredData || {});
        setTotalStats({
          pob: data?.total_pob || 0,
          doctors: data?.total_doctors || 0,
        });
      })
      .catch(() => setStateData({}))
      .finally(() => setLoading(false));
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleDownload = () => {
    setLoading(true);
    Axios({ url: "/admin/all-scans", method: "GET", responseType: "blob" })
      .then(({ data }) => {
        downloadExcelFile(data, "all_scans.xlsx");
      })
      .finally(() => setLoading(false));
  };

  return (
    <div className="relative flex min-h-screen flex-col items-center bg-gray-50">
      <Header />
      <div className="my-8 flex w-11/12 max-w-4xl flex-col items-center justify-between gap-5 rounded-2xl border border-gray-100 bg-white px-8 py-4 shadow-xl md:w-full md:flex-row">
        <h2 className="text-md font-semibold tracking-tight text-gray-800 md:text-2xl">
          All India POB collection
        </h2>
        <button
          onClick={handleDownload}
          className="bg-primaryv2 hover:bg-primaryv7 disabled:bg-primaryv2/40 rounded-lg px-5 py-2 font-medium text-white shadow transition-colors duration-150"
          disabled={loading || totalStats?.pob === 0}
        >
          {loading ? "Downloading..." : "Download Data"}
        </button>
      </div>
      <div className="relative mb-0 flex w-11/12 max-w-4xl items-center justify-center rounded-2xl border border-gray-100 bg-white p-0 shadow-lg md:mb-8 md:w-full md:p-6">
        <ComposableMap
          projection="geoMercator"
          projectionConfig={{ scale: 1000, center: [82, 23] }}
        >
          <Geographies geography={IndiaTopoJson}>
            {({ geographies }) =>
              geographies.map((geo) => {
                const stateName =
                  geo.properties.st_nm ||
                  geo.properties.state ||
                  geo.properties.name;
                const pobCount = Number(stateData[stateName]?.pob_count || 0);
                const doctorCount = Number(
                  stateData[stateName]?.doctor_count || 0,
                );
                const opacityPercentage = Math.floor(
                  (pobCount * 100 * 255) / (totalStats?.pob * 100),
                );
                const fillColor =
                  opacityPercentage > 0
                    ? `#045a8d${opacityPercentage.toString(16)?.padStart(2, "0")}`
                    : "#ffffff";

                console.log("fillColor", fillColor);
                return (
                  <Geography
                    key={geo.rsmKey}
                    geography={geo}
                    fill={fillColor}
                    stroke="#b1bce3"
                    strokeWidth={0.7}
                    vectorEffect="non-scaling-stroke"
                    onClick={() =>
                      setActiveState({
                        state: stateName,
                        pob_count: pobCount,
                        doctor_count: doctorCount,
                      })
                    }
                    style={{
                      default: {
                        outline: "none",
                        transition: "fill 0.2s",
                      },
                      hover: {
                        fill: "#00629d",
                        outline: "none",
                        cursor: "pointer",
                      },
                      pressed: {
                        outline: "none",
                      },
                    }}
                  >
                    <title>{`${stateName}: ${pobCount}`}</title>
                  </Geography>
                );
              })
            }
          </Geographies>
        </ComposableMap>

        <div className="text-primaryv2 absolute right-2 bottom-4 text-xs md:right-4 md:text-sm">
          <p>
            Total Doctors - <strong>{totalStats?.doctors}</strong>
          </p>
          <p>
            Total POB Collected - <strong>{totalStats?.pob}</strong>
          </p>
        </div>
      </div>
      {activeState && (
        <div className="my-8 flex w-11/12 max-w-4xl flex-col items-center justify-between gap-5 rounded-2xl border border-gray-100 bg-white px-8 py-4 shadow-xl md:w-full md:flex-row">
          <h2 className="text-base font-medium tracking-tight text-gray-800 md:text-2xl">
            {activeState?.state}{" "}
            <span className="font-semibold text-gray-500">
              - {activeState?.pob_count} POB Collected
            </span>
          </h2>
          <h2 className="text-base font-medium tracking-tight text-gray-800 md:text-2xl">
            Doctors{" "}
            <span className="font-semibold text-gray-500">
              - {activeState?.doctor_count}
            </span>
          </h2>
        </div>
      )}

      {/* Minimalist table for stateData */}
      {stateData && Object.keys(stateData).length > 0 && (
        <div className="my-8 w-11/12 max-w-4xl overflow-x-auto rounded-2xl border border-gray-100 bg-white shadow-xl">
          <table className="min-w-full text-left">
            <thead>
              <tr className="bg-primary/20">
                <th className="px-6 py-3 text-xs font-semibold text-gray-700">
                  State
                </th>
                <th className="px-6 py-3 text-xs font-semibold text-gray-700">
                  POB Collected
                </th>
              </tr>
            </thead>
            <tbody>
              {Object.entries(stateData).map(([state, stats]) => (
                <tr
                  key={state}
                  className="border-t border-gray-100 hover:bg-gray-50"
                >
                  <td className="px-6 py-2 text-sm text-gray-800">{state}</td>
                  <td className="px-6 py-2 text-sm text-gray-800">
                    {stats?.pob_count}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
};

export default ManagerDashboard;

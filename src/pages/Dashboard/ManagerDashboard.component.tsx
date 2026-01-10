import { useEffect, useState } from "react";
import Header from "../../components/Header/Header.component";
import Axios from "../../utils/axios";
import { downloadExcelFile } from "../../utils/downloadExcel.util";

import { ComposableMap, Geographies, Geography } from "react-simple-maps";
import IndiaTopoJson from "../../maps/IND.json";

const ManagerDashboard = () => {
  const [stateData, setStateData] = useState([]);
  const [totalScans, setTotalScans] = useState(0);
  const [activeState, setActiveState] = useState(null);
  const [loading, setLoading] = useState(false);

  const fetchData = () => {
    setLoading(true);
    Axios({ url: "/admin/all-scans-by-state", method: "GET" })
      .then(({ data }) => {
        const structuredData = data?.data?.reduce((prev, curr) => {
          prev[curr?.state] = curr?.count;
          return prev;
        }, {});
        setStateData(structuredData || {});
        setTotalScans(data?.total || 0);
      })
      .catch(() => setStateData([]))
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
          All India Scans{" "}
          <span className="font-normal text-gray-500">({totalScans})</span>
        </h2>
        <button
          onClick={handleDownload}
          className="bg-primaryv2 hover:bg-primaryv7 disabled:bg-primaryv2/40 rounded-lg px-5 py-2 font-medium text-white shadow transition-colors duration-150"
          disabled={loading || totalScans === 0}
        >
          {loading ? "Downloading..." : "Download Data"}
        </button>
      </div>
      <div className="mb-0 flex w-11/12 max-w-4xl items-center justify-center rounded-2xl border border-gray-100 bg-white p-0 shadow-lg md:mb-8 md:w-full md:p-6">
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
                const value = Number(stateData[stateName] || 0);
                const opacityPercentage = Math.floor(
                  (value * 100 * 255) / (totalScans * 100),
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
                      setActiveState({ state: stateName, count: value })
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
                    <title>{`${stateName}: ${value}`}</title>
                  </Geography>
                );
              })
            }
          </Geographies>
        </ComposableMap>
      </div>
      {activeState && (
        <div className="my-8 flex w-11/12 max-w-4xl flex-col items-center justify-between gap-5 rounded-2xl border border-gray-100 bg-white px-8 py-4 shadow-xl md:hidden md:w-full md:flex-row">
          <h2 className="text-base font-medium tracking-tight text-gray-800 md:text-2xl">
            {activeState?.state}{" "}
            <span className="font-semibold text-gray-500">
              - {activeState?.count} Scans
            </span>
          </h2>
        </div>
      )}
    </div>
  );
};

export default ManagerDashboard;

import Header from "../../components/Header/Header.component";

const Home = () => {
  return (
    <div className="flex h-screen flex-col">
      <Header />
      <div className="flex h-screen flex-1 flex-col md:flex-row">
        <video
          className="h-full w-full object-cover md:w-1/2"
          muted
          loop
          autoPlay
          src="/videos/hero-video1.mp4"
        />
        <video
          className="h-full w-full object-cover md:w-1/2"
          muted
          loop
          autoPlay
          src="/videos/hero-video2.mp4"
        />
      </div>
    </div>
  );
};

export default Home;

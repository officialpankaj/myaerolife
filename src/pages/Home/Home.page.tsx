import Header from "../../components/Header/Header.component";

const Home = () => {
  return (
    <div className="relative flex h-screen flex-col">
      <div className="top-0 left-0 w-full md:absolute">
        <Header />
      </div>

      <div className="flex h-screen w-full flex-1 flex-col md:flex-row">
        {/* <video
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
        /> */}
        <div className="w-full">
          <iframe
            src="https://player.vimeo.com/video/1198482616?badge=0&autopause=0&player_id=0&app_id=58479&autoplay=1"
            allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share"
            referrerPolicy="strict-origin-when-cross-origin"
            className="aspect-video w-full"
            title="2JuneAerolifeMiniUsageFilm"
          />
        </div>
      </div>
    </div>
  );
};

export default Home;

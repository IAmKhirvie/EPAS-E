<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InformationSheetContentSeeder extends Seeder
{
    public function run()
    {
        DB::beginTransaction();

        try {
            $course = Course::firstOrCreate(
                ['course_code' => 'EPAS-NCII'],
                [
                    'course_name' => 'Electronic Products Assembly and Servicing NCII',
                    'description' => 'This course covers the competencies required to assemble and service electronic products according to industry standards.',
                    'sector' => 'Electronics',
                    'is_active' => true,
                    'order' => 1
                ]
            );

            $module = Module::firstOrCreate(
                ['module_number' => 'Module 1', 'course_id' => $course->id],
                [
                    'sector' => 'Electronics',
                    'qualification_title' => 'Electronic Products Assembly And Servicing NCII',
                    'unit_of_competency' => 'Assemble Electronic Products',
                    'module_title' => 'Assembling Electronic Products',
                    'module_name' => 'Competency Based Learning Material',
                    'how_to_use_cblm' => 'Welcome to the Module "Assembling Electronic Products". This module contains training materials and activities for you to complete.

The unit of competency "Assemble Electronic Products" contains the knowledge, skills and attitudes required for Electronic Products Assembly and Servicing course.

You are required to go through a series of learning activities in order to complete each of the learning outcomes of the module.',
                    'introduction' => 'This module contains information sheet(s) and suggested learning activities in Assembling Electronic Products. It includes instructions and procedure on how to Assemble Electronic Products.

This module consists of five (5) learning outcomes.',
                    'learning_outcomes' => 'Upon completion of the module the students shall be able to:
1. Prepare to assemble electronics products
2. Prepare/Make PCB modules
3. Mount and solder electronic components
4. Assemble electronic products
5. Test and inspect assembled electronic products',
                    'is_active' => true,
                    'order' => 1,
                ]
            );


            // ===== Information Sheet 1.1 =====
            $sheet1 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.1'],
                [
                    'title' => 'Introduction to Basic Electronics and Electricity',
                    'content' => 'Introduction to Basic Electronics and Electricity',
                    'order' => 1,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet1->id)->delete();

            // Slide 1: Electric History (intro only)
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Electric History',
                'content' => '<p>For hundreds of years electricity has fascinated many scientists. Around 600 BC, Greek philosophers discovered that by rubbing amber against a cloth, lightweight objects would stick to it. Just like rubbing a balloon on a cloth makes the balloon stick to other objects.</p><p>It was not until around the year 1600, that any real research was done on this phenomenon. A scientist by the name of Dr. William Gilbert researched the effects of amber and magnets and wrote the theory of magnetism. In fact, Dr. Gilbert was the first to use the word electric in his theory.</p><p>Dr. William Gilbert\'s research and theories opened the door for more discoveries into magnetism and the development of electricity.</p><p>Electricity is produced when the electrons flow on a conductor.</p>',
                'order' => 1,
            ]);

            // Slide 1.1: James Watt
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'James Watt (1736-1819)',
                'content' => '',
                'order' => 2,
                'parts' => [[
                    'title' => 'James Watt (1736-1819)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Watt_James_von_Breda.jpg/330px-Watt_James_von_Breda.jpg',
                    'explanation' => 'James Watt was a Scottish inventor who made improvements to the steam engine during the late 1700s. Soon, factories and mining companies began to use Watt\'s new-and-improved steam engine for their machinery. This helped jumpstart the Industrial Revolution, a period in the early 1800s that saw many new machines invented and an increase in the number of factories. After his death, Watt\'s name was used to describe the electrical unit of power.',
                ]],
            ]);

            // Slide 1.2: Alessandro Volta
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Alessandro Volta (1745-1827)',
                'content' => '',
                'order' => 3,
                'parts' => [[
                    'title' => 'Alessandro Volta (1745-1827)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Alessandro_Volta.jpeg/330px-Alessandro_Volta.jpeg',
                    'explanation' => 'Using zinc, copper and cardboard, this Italian professor invented the first battery. Volta\'s battery produced a reliable, steady current of electricity. The unit of voltage is now named after Volta.',
                ]],
            ]);

            // Slide 1.3: Andre-Marie Ampere
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Andre-Marie Ampere (1775-1836)',
                'content' => '',
                'order' => 4,
                'parts' => [[
                    'title' => 'Andre-Marie Ampere (1775-1836)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Ampere_Andre_1825.jpg/330px-Ampere_Andre_1825.jpg',
                    'explanation' => 'Andre-Marie Ampere, a French physicist and science teacher, played a big role in discovering electromagnetism. He also helped describe a way to measure the flow of electricity. The ampere, which is the unit for measuring electric current, was named in honour of him.',
                ]],
            ]);

            // Slide 1.4: Georg Ohm
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Georg Ohm (1787-1854)',
                'content' => '',
                'order' => 5,
                'parts' => [[
                    'title' => 'Georg Ohm (1787-1854)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Georg_Simon_Ohm_%281789-1854%29.jpg/330px-Georg_Simon_Ohm_%281789-1854%29.jpg',
                    'explanation' => 'German physicist and teacher Georg Ohm researched the relationship between voltage, current and resistance. In 1827, he proved that the amount of electrical current that can flow through a substance depends on its resistance to electrical flow. This is known as Ohm\'s Law.',
                ]],
            ]);

            // Slide 1.5: Michael Faraday
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Michael Faraday (1791-1867)',
                'content' => '',
                'order' => 6,
                'parts' => [[
                    'title' => 'Michael Faraday (1791-1867)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Michael_Faraday_sitting_crop.jpg/330px-Michael_Faraday_sitting_crop.jpg',
                    'explanation' => 'Michael Faraday, a British physicist and chemist, was the first person to discover that moving a magnet near a coil of copper wire produced an electric current in the wire.',
                ]],
            ]);

            // Slide 1.6: Henry Woodward
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Henry Woodward',
                'content' => '',
                'order' => 7,
                'parts' => [[
                    'title' => 'Henry Woodward (exact birth and death unknown)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/8/8c/Light_bulb_patent.jpg',
                    'explanation' => 'Henry Woodward, a Canadian medical student, played a major role in developing the electric light bulb. In 1874, Woodward and a colleague named Mathew Evans placed a thin metal rod inside a glass bulb. They forced the air out of the bulb and replaced it with a gas called nitrogen. The rod glowed when an electric current passed through it, creating the first electric lamp. Unfortunately, Woodward and Evans couldn\'t afford to develop their idea further. So in 1889, they sold their patent to Thomas Edison.',
                ]],
            ]);

            // Slide 1.7: Thomas Edison
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Thomas Edison (1847-1931)',
                'content' => '',
                'order' => 8,
                'parts' => [[
                    'title' => 'Thomas Edison (1847-1931)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Thomas_Edison2.jpg/330px-Thomas_Edison2.jpg',
                    'explanation' => 'American inventor Thomas Edison purchased Henry Woodward\'s patent and began to work on improving the idea. He attached wires to a thin strand of paper, or filament, inside a glass globe. The filament began to glow, which generated some light. This became the first incandescent light bulb. A thin, iron wire later replaced the paper filament.',
                ]],
            ]);

            // Slide 1.8: Nikola Tesla
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Nikola Tesla (1856-1943)',
                'content' => '',
                'order' => 9,
                'parts' => [[
                    'title' => 'Nikola Tesla (1856-1943)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Tesla_circa_1890.jpeg/330px-Tesla_circa_1890.jpeg',
                    'explanation' => 'A Serbian inventor named Nikola Tesla invented the first electric motor by reversing the flow of electricity on Thomas Edison\'s generator. In 1885, he sold his patent rights to an American businessman who was the head of the Westinghouse Electric Company. In 1893, the company used Tesla\'s ideas to light the Chicago World\'s Fair with a quarter of a million lights.',
                ]],
            ]);

            // Slide 1.9: Sir Adam Beck
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Sir Adam Beck (1857-1925)',
                'content' => '',
                'order' => 10,
                'parts' => [[
                    'title' => 'Sir Adam Beck (1857-1925)',
                    'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f5/Sir_Adam_Beck_%28F1257_s1057_it2415%29_%28cropped%29.jpg/330px-Sir_Adam_Beck_%28F1257_s1057_it2415%29_%28cropped%29.jpg',
                    'explanation' => 'In the early 1900s, manufacturer and politician Sir Adam Beck pointed out that private power companies were charging customers too much for electricity. He believed that all citizens had the right to cheap electric light and power. So he worked to get the Ontario government to create the Hydro-Electric Power Commission in 1910. Because of his efforts, he earned the nickname The Hydro Knight.',
                ]],
            ]);

            // Remaining Sheet 1.1 topics (after 9 scientist slides)
            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Free Electrons',
                'content' => '<p><em>Heat</em> is only one of the types of energy that can cause electrons to be forced from their orbits. A <strong>magnetic field</strong> can also be used to cause electrons to move in a given direction. <em>Light energy</em> and <em>pressure</em> on a crystal are also used to generate electricity by forcing electrons to flow along a given path. When</p><p>electrons leave their orbits, they move from atom to atom at random, drifting in no particular direction. Electrons that move in such a way are referred to as free electrons. However, a force can be used to cause them to move in a given direction. That is how electricity (the flow of electrons along a conductor) is generated.</p><p>The electrons in the outermost orbit are called <em>valence electrons.</em> If a valence electron acquires a sufficient amount of energy, it can escape from the outer orbit. The escaped valence electron is called a <em>free electron.</em> It can migrate easily from one atom to another.</p><p>Shown here is the maximum numbers of electrons allowed in each shell: 2, 8, 18, 32, 32, 18, 2</p>',
                'order' => 11,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Introduction to Sources of Electricity',
                'content' => '<p><strong>Sources of electricity</strong> are everywhere in the world. Worldwide, there is a range of energy resources available to generate electricity. These energy resources fall into two main categories, often called renewable and non-renewable energy resources. Each of these resources can be used as a source to generate electricity, which is a very useful way of transferring energy from one place to another such as to the home or to industry.</p><p>There are to Categories of Sources of Electricity which is:</p><p><em>Renewable</em> sources of energy can consider the natural element such as water, volcano and wind that is used to create energy by the help of a turbine and other element that can produce energy by using sunlight.</p><p>Non-renewable sources of energy can be divided into two types: fossil fuels and nuclear fuel.</p>',
                'order' => 12,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'How is power generated?',
                'content' => '<p>An electric generator is a device for converting mechanical energy into electrical energy. The process is based on the relationship between magnetism and power. When a wire or any other electrically conductive material moves across a magnetic field, an electric current occurs in the wire. The large generators used by the electric utility industry have a stationary conductor. A magnet attached to the end of a spinning coil of wire rotating shaft is positioned inside a stationary conducting ring that is wrapped with a long, continuous piece of wire. When the magnet rotates, it induces a small electric current in each section of wire as it passes. Each section of wire constitutes a small, separate electric conductor. All the small currents of individual sections add up to one current of considerable size. This current is used for electric power.</p>',
                'order' => 13,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Turbines and Power Generation',
                'content' => '<p>An electric utility power station uses either a turbine, engine, water wheel, or other similar machine to drive an electric generator or a device that converts mechanical or chemical energy to power. Steam turbines, internal-combustion engines, gas combustion turbines, water turbines, and wind turbines are the most common methods to generate power.</p><p><strong>Most of the power in the United States is produced through the use of steam turbines in power plants.</strong> A turbine converts the kinetic energy of a moving fluid (liquid or gas) to mechanical energy. Steam turbines have a series of blades mounted on a shaft against which steam is forced, thus rotating the shaft connected to the generator. In a fossil-fueled steam turbine, the fuel is burned in a furnace to heat water in a boiler to produce steam.</p><p><strong><em>Coal, petroleum (oil), and natural gas</em></strong> are burned in large furnaces to heat water to make steam that in turn pushes on the blades of a turbine. Did you know power in the United States? In 2000, more than half (52%) of the county\'s <strong>3.8 trillion</strong>kilowatthours used coal as its primary source of thermal generated energy in power stations.</p><p>How it works</p><p>1. Conveyor, after the coal arrives at the plant and is processed, it is delivered to the coal hopper to be crushed down to 2 inches. It is then delivered by a conveyor belt to the pulverizer.</p><p>2. Pulverizer crushes the coal into a very fine powder. This coal powder is then mixed with air and the powder blown into the furnace or boiler for combustion.</p><p>3. Water Purification, water must be purified before it can be used in the boiler tubes, to minimize corrosion, and once treated it is called boiler feed water.</p><p>4. Boiler, inside the boiler, the coal and air ignites instantly. Large amounts of boiler fed water are pumped through tubes that run inside the boiler. The intense heat created from the burning coal vaporizes the feed water inside the tubes to create steam.</p><p>5. Precipitator Scrubber, the precipitator scrubber is like a giant air filter. Burning coal produces ash and other gas emissions. These gases and fly ash have to be vented from the boiler. The precipitator captures and removes up to 99.4 percent of the ash before it reaches the stacks and is vented.</p><p>6. Stack, after the gases and fly ash have been collected in the precipitator, the remaining flue gases are dissipated into the atmosphere through large stacks.</p><p>7. Steam Turbine, the steam-turbine is a giant drum with thousands of propeller blades. The high-pressure steam from the boilers travels into the turbine blades causing it to spin.</p><p>8. Generator, the spinning turbine causes the shaft to turn inside the generator creating electric energy in the form of voltage and current.</p><p>9. Transformer, the voltage in the electricity is then increased and the current decreased by a transformer before the electricity flows into the transmission system in order to travel long distances to substations.</p><p>10. Distributions to homes and business, the substations then reduce the voltage flow into the distribution lines in cities and towns. The voltage is again reduced by smaller transformers before reaching the consumer.</p><p>11. Condensers, condensers circulate cool water which cools down and condenses the steam after it is used in the boiler, and discharged by the turbine. The cool water is warmed by the steam, which condenses back into the boiler.</p><p>12. Cooling pond, depending on the source of the condenser cooling water, the warmed water may be reused by cooling it in a cooling pond or returned to the river, lake or reservoir from which it came.</p><p><em>Natural gas,</em> in addition to being burned to heat water for steam, can also be burned to produce hot combustion gases that pass directly through a turbine, spinning the blades of the turbine to generate power. Gas turbines are commonly used when power utility usage is in high demand. In 2000, 16% of the nation\'s power was fueled by natural gas.</p><p><em>Petroleum</em> can also be used to make steam to turn a turbine. Residual fuel oil, a product refined from crude oil, is often the petroleum product used in electric plants that use petroleum to make steam. Petroleum was used to generate less than three percent (3%) of all power generated in U.S. power plants in 2000.</p><p><strong><em>Nuclear power</em></strong> is a method in which steam is produced by heating water through a process called nuclear fission. In nuclear power plants, a reactor contains a core of nuclear fuel, primarily enriched uranium. When atoms of uranium fuel are hit by neutrons they fission (split), releasing heat and more neutrons. Under controlled conditions, these other neutrons can strike more uranium atoms, splitting more atoms, and so on. Thereby, continuous fission can take place, forming a chain reaction releasing heat. The heat is used to turn water into steam, that, in turn, spins a turbine that generates power. Nuclear power is used to generate 20% of all the country\'s power.</p><p>Nuclear energy is energy that is stored in the nucleus or center core of an atom. The nucleus of an atom is made of tiny particles of protons (+ positive charge) and neutrons (no charge). The electrons (- negative charge) move around the nucleus. The nuclear energy is what holds the nucleus together.</p><p>How it works</p><p>In order to use this energy, it has to be released from the atom. There are two ways to free the energy inside the atom.</p><strong>1. Fusion</strong><p>Fusion is a way of combining the atoms to make a new atom.</p><p>For example, the energy from the sun is produced by fusion. Inside the sun, hydrogen atoms are combined to make helium. Helium doesn\'t need that much energy to hold it together, so the extra energy produced is released as heat and light.</p><strong>2. Fission</strong><p>Fission is a way of splitting an atom into two smaller atoms. The two smaller atoms don\'t need as much energy to hold them together as the larger atom, so the extra energy is released as heat and radiation.</p><p>Nuclear power plants use fission to make electricity. By splitting <strong>uranium</strong> atoms into two smaller atoms, the extra energy is released as heat. Uranium is a mineral rock, a very dense metal, that is found in the ground and is non-renewable, that means we can\'t make more. It is a cheap and plentiful fuel source. Power plants use the heat given off during fission as fuel to make electricity.</p><p>Fission creates heat which is used to boil water into steam inside a reactor. The steam then turns huge turbines that drive generators that make electricity. The steam is then changed back into water and cooled down in a cooling tower. The water can then be used over and over again.</p><p><strong><em>Hydropower,</em></strong> the source for <strong>7%</strong> of U.S. power generation, is a process in which flowing water is used to spin a turbine connected to a generator. There are two basic types of hydroelectric systems that produce power. In the first system, flowing water accumulates in reservoirs created by the use of dams. The water falls through a pipe called a penstock and applies pressure against the turbine blades to drive the generator to produce power. In the second system, called run-of-river, the force of the river current (rather than falling water) applies pressure to the turbine blades to produce power.</p><p>Hydropower was later used to generate electricity. After the invention of the turbine in the early 1800\'s and the generator in the late 1800\'s, the first hydroelectric plant in the U.S. was built at Niagara Falls in 1879. Niagara Falls borders New York and Canada. The gravity caused the water from the high ground to fall to the lower ground over the dam into a reservoir. The energy force from the falling water was used to turn a turbine in the dam and generate electricity.</p>',
                'order' => 14,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Alternative Energy',
                'content' => '<p>Alternative Energy comes from resources like the sun (solar), the earth (geothermal), the wind (wind power), wood, agricultural crops and animal waste (biomass), landfill or methane gasses (biogas), and other sources like fuel cells. These resources are abundant and are renewable fuels. By using alternative fuel sources we can conserve our non-renewable fuel sources like natural gas and oil. By doing this we can be more energy efficient in producing electricity and heat while protecting our environment.</p><p><strong><em>Geothermal power</em></strong> comes from heat energy buried beneath the surface of the earth. In some areas of the country, enough heat rises close to the surface of the earth to heat underground water into steam, which can be tapped for use at steam-turbine plants.</p><strong>There are several different main types of geothermal plants:</strong><ul><li>Dry steam</li><li>Flash steam</li><li>Binary cycle</li></ul><p>What these types of geothermal power plants all have in common is that they <strong>use steam turbines to generate electricity.</strong> This approach is very similar to other thermal power plants using other sources of energy than geothermal.</p><p>Water or working fluid is heated (or used directly incase of geothermal dry steam power plants), and then sent through a steam turbine where the thermal energy (heat) is converted to electricity with a generator through a phenomenon called <strong>electromagnetic induction.</strong> The next step in the cycle is cooling the fluid and sending it back to the heat source.</p><p>Water that has been seeping into the underground over time has gained heat energy from the geothermal reservoirs. There no need for additional heating, as you would expect with other thermal power plants. <strong>Heating boilers are not present in geothermal steam power plants and no heating fuel is used.</strong></p><p><em>Production wells</em> (red on the illustrations) are used to lead hot water/steam from the reservoirs and into the power plant.</p><p><em>Rock catchers</em> are in place to make sure that only hot fluids is sent to the turbine. Rocks can cause great damage to steam turbines.</p><p><em>Injection wells</em> (blue on the illustrations) ensure that the water that is drawn up from the production wells returns to the geothermal reservoir where it regains the thermal energy (heat) that we have used to generate electricity.</p><h4>Geothermal Dry Steam Power Plants</h4><p>This type of geothermal power plant was named dry steam since <strong>water water that is extracted from the underground reservoirs has to be in its gaseous form (water-vapor).</strong></p><p><strong>Geothermal steam of at least 150°C (300°F)</strong> is extracted from the reservoirs through the production wells (as we would do with all geothermal power plant types), but is then sent directly to the turbine. Geothermal reservoirs that can be exploited by geothermal dry steam power plants are rare.</p><p><em>Dry steam</em> is the oldest geothermal power plant type. The first one was constructed in Larderello, Italy, in 1904. The Geysers, 22 geothermal power plants located in California, is the only example of geothermal dry steam power plants in the United States.</p><h4>Geothermal Flash Steam Power Plants</h4><p>Geothermal flash steam power plants uses <strong>water at temperatures of at least 182°C (360°F).</strong> The term flash steam refers the process where high-pressure hot water is flashed (vaporized) into steam inside a flash tank by lowering the pressure. This steam is then used to drive around turbines.</p><p><em>Flash steam</em> is today\'s most common power plant type. The first geothermal power plant that used flash steam technology was the <strong>Wairakei Power station in New Zealand,</strong> which was built already in 1958.</p><h4>Geothermal Binary Cycle Power Plants</h4><p>The binary cycle power plant has one major advantage over flash steam and dry steam power plants: <strong>The water-temperature can be as low as 57°C (135°F).</strong></p><p><strong>By using a working fluid (binary fluid) with a much lower boiling temperature than water,</strong> thermal energy in the reservoir water flashes the working fluid into steam, which then is used to generate electricity with the turbine. The water coming from the geothermal reservoirs through the production wells is <strong>never in direct contact with the working fluid.</strong> After the some of its thermal energy is transferred to the working fluid with a heat exchanger, the water is sent back to the reservoir through the injection wells where it regains it\'s thermal energy.</p><p><strong><em>Solar power</em></strong> is derived from the energy of the sun. However, the sun\'s energy is not available full-time and it is widely scattered. The processes used to produce power using the sun\'s energy have historically been more expensive than using conventional fossil fuels. Photovoltaic conversion generates electric power directly from the light of the sun in a photovoltaic (solar) cell. Solar-thermal electric generators use the radiant energy from the sun to produce steam to drive turbines. Less than 1% of the nation\'s generation is based on solar power.</p><p>Solar electricity is created by using Photovoltaic (PV) technology by converting solar energy into solar electricity from sunlight. Photovoltaic systems use sunlight to power ordinary electrical equipment, for example, household appliances, computers and lighting. The photovoltaic (PV) process converts free solar energy - the most abundant energy source on the planet - directly into solar power.</p><p>The components typically required in a grid-connected PV system are illustrated below.</p><p>The <strong>PV array</strong> consists of a number of individual photovoltaic modules connected together to give the required power with a suitable current and voltage output.</p><p>PV equipment has no moving parts and as a result requires minimal maintenance. It generates solar electricity without producing emissions of greenhouse or any other gases, and its operation is virtually silent.</p><p><strong><em>Wind power</em></strong> is derived from the conversion of the energy contained in wind into power. Wind power, like the sun, is rapidly growing source of power, and is used for less than 1% of the nation\'s power. A wind turbine is similar to a typical wind mill.</p><p><strong><em>Biomass</em></strong> includes wood, municipal solid waste (garbage), and agricultural waste, such as corn cobs and wheat straw. These are some other energy sources for producing power. These sources replace fossil fuels in the boiler. The combustion of wood and waste creates steam that is typically used in conventional steam-electric plants. Biomass accounts for less than 1% of the power generated in the United States.</p><p>The power produced by a generator travels along cables to a transformer, which changes power from low voltage to high voltage. Power can be moved long distances more efficiently using high voltage. Transmission lines are used to carry the power to a substation. Substations have transformers that change the high voltage power into lower voltage power. From the substation, distribution lines carry the power to homes, offices and factories, which require low voltage power.</p>',
                'order' => 15,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Types of Electric Energy',
                'content' => '<p><em>Potential energy</em> (static electricity), is an electricity at rest, can be called energy due to position or composition. Ex; flash light batteries, car batteries.</p><p><em>Kinetic energy</em> (current electricity/dynamic electricity), is an electricity in motion or energy of motion. Ex; when electrical charges stores in battery moves or flow to perform useful work.</p>',
                'order' => 16,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Electric Current',
                'content' => '<p>Electric current is the flow of electrons, but electrons do not jump directly from the origin point of the current to the destination. Instead, each electron moves a short distance to the next atom, transferring its energy to an electron in that new atom, which jumps to another atom, and so on.</p><strong>Types of Electric Current</strong><p><strong><em>Direct Current</em></strong> is electric current that only flows in one direction. A common place to find direct current is in batteries. A battery is first charged using direct current that is then transformed into chemical energy. When the battery is in use, it turns the chemical energy back into electricity in the form of direct current. Batteries need direct current to charge up, and will only produce direct current.</p><p><strong><em>Alternating Current</em></strong> as the name implies, alternates in direction. Alternating current is used for the production and transportation of electricity. This is because when electricity is produced in large scale, such as in a power plant, it has dangerously high voltage. It is easier and cheaper to downgrade this current to lower voltage for home use when the current is AC. However, there was another factor that helped determine the choice of AC as the current of choice for domestic consumption. In the late 19th century, an industrial struggle between the Westinghouse Company, which used AC, and General Electric, which used DC, ended in AC\'s favor when Westinghouse successfully lit the 1893 Chicago World\'s Fair using AC. Since then, alternating current powers homes and anything else that draws on the current in power lines.</p><p>Here are some of the Advantages and Disadvantages of both current:</p><p><strong>AC</strong> is easy to produce, easy to amplify, but not stable and cannot store; while,</p><p><strong>DC</strong> is not easy to produce, not easy to amplify, but stable and can be store.</p>',
                'order' => 17,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Conductors, Insulators and Semi-conductors',
                'content' => '<p><strong>Conductors</strong> are made of materials that electricity can flow through easily. These materials are made up of atoms whose electrons can move away freely. Gold is considered as best conductor because of it\'s atomic No. of elements.</p><p>Here are some examples of Conductive materials:</p><ul><li>Copper</li><li>Aluminum</li><li>Platinum</li><li>Gold</li><li>Silver</li><li>Water</li><li>People and Animals</li></ul><p><strong>Insulators</strong> are materials opposite of conductors. The atoms are not easily freed and are stable, preventing or blocking the flow of electricity.</p><p>Here are some examples of Conductive materials:</p><ul><li>Glass</li><li>Porcelain</li><li>Plastic</li><li>Rubber</li></ul><p>Electricity will always take the shortest path to the ground. Your body is 60% water and that makes you a good <strong>conductor</strong> of electricity. If a power line has fallen on a tree and you touch the tree you become the path or conductor to the ground and could get electrocuted.</p><p>The rubber or plastic on an electrical cord provides an <strong>insulator</strong> for the wires. By covering the wires, the electricity cannot go through the rubber and is forced to follow the path on the aluminum or copper wires.</p>',
                'order' => 18,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet1->id,
                'title' => 'Semi-Conductors',
                'content' => '<p>A semiconductor is a material that has intermediate conductivity between a conductor and an insulator. It means that it has unique physical properties somewhere in between a conductor like aluminum and an insulator like glass. In a process called doping, small amounts of impurities are added to pure semiconductors causing large changes in the conductivity of the material. Examples include silicon, the basic material used in the integrated circuit, and germanium, the semiconductor used for the first transistors.</p><p>By controlling the amount of impurities added to the semiconductor material it is possible to control its conductivity. These impurities are called donors or acceptors depending on whether they produce electrons or holes respectively.</p><p>This process of adding impurity atoms to semiconductor atoms (the order of 1 impurity atom per 10 million (or more) atoms of the semiconductor) is called Doping.</p><p>The most commonly used semiconductor basics material by far is silicon. Silicon has four valence electrons in its outermost shell which it shares with its neighboring silicon atoms to form full orbital\'s of eight electrons. The structure of the bond between the two silicon atoms is such that each atom shares one electron with its neighbor making the bond very stable.</p><p>As there are very few free electrons available to move around the silicon crystal, crystals of pure silicon (or germanium) are therefore good insulators, or at the very least very high value resistors.</p><p>Silicon atoms are arranged in a definite symmetrical pattern making them a crystalline solid structure. A crystal of pure silica (silicon dioxide or glass) is generally said to be an intrinsic crystal (it has no impurities) and therefore has no free electrons.</p>',
                'order' => 19,
            ]);


            // ===== Information Sheet 1.2 =====
            $sheet2 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.2'],
                [
                    'title' => 'Resistors, Color Coding, Conversion, Tolerance, Circuits and Ohm\'s Law',
                    'content' => 'Resistors, Color Coding, Conversion, Tolerance, Circuits and Ohm\'s Law',
                    'order' => 2,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet2->id)->delete();

            Topic::create([
                'information_sheet_id' => $sheet2->id,
                'title' => 'Electronic Components',
                'content' => '<h4>DEVICES: RESISTORS</h4><p>A <strong>resistor</strong> is an electronic device that limits or opposes the amount of current in a circuit. A resistor has two terminals across which electricity must pass, and is designed to drop the voltage of the current as it flows from one terminal to the next. A resistor is primarily used to create and maintain a known safe current within an electrical component.</p><p>Every resistor falls into one of two categories: fixed or variable.</p><p>1. Fixed resistor - has a predetermined amount of resistance to current</p><p>2. Variable resistor - can be adjusted to give different levels of resistance. Examples are the carbon composition and wirewound resistors. Variable resistors are also called potentiometers and are commonly used as volume controls on audio devices.</p><h4>Resistor Color Coding</h4><p>Resistor - 4 Bands</p><p>The color code chart is applicable to most of the common four-band and five-band resistors. Five-band resistors are usually precision resistors with tolerances of 1% and 2%. Most of the four-band resistors have tolerances of 5%, 10% and 20%.</p><p>The color codes of a resistor are read from left to right, with the tolerance band oriented to the right side. Match the color of the first band to its associated number under the digit column in the color chart. This is the first digit of the resistance value. Match the second band to its associated color under the digit column in the color chart to get the second digit of the resistance value.</p><p>Match the color band preceding the tolerance band (last band) to its associated number under the multiplier column on the chart. This number is the multiplier for the quantity previously indicated by the first two digits (four band resistor) or the first three digits (five band resistor) and is used to determine the total marked value of the resistor in ohms (see Ohm\'s Law, below).</p><h4>Tolerance</h4><p>Is the maximum and minimum accepted value of resistor when measuring it thru multi-tester, by multiplying the percentage of the fourth or fifth colored value of the resistor on the first and second color, then after it adding and subtracting, adding it on the value of the resistor first and second color will get the maximum accepted value of the resistor and subtracting it will get the minimum.</p><p>brown, black, red, gold (5% tolerance) - Sample computation:</p><p>1000 - resistor\'s value</p><p>x 0.05 - resistor\'s 5% value</p><p>50 - conversion</p><p>1000 - resistor\'s value / 50 - resistor\'s 5% value</p><p>1050 - maximum tolerance</p><p>1000 - resistor\'s value / 50 - resistor\'s 5% value</p><p>950 - minimum tolerance</p><p>brown, black, red, silver (10% tolerance) - Sample computation:</p><p>1000 - resistor\'s value</p><p>x 0.1 - resistor\'s tolerance</p><p>100 - resistor\'s 10% value</p><p>1000 - resistor\'s value / 100 - resistor\'s 20% value</p><p>1100 - maximum tolerance</p><p>1000 - resistor\'s value / 100 - resistor\'s 10% value</p><p>900 - minimum tolerance</p><h4>How to test the Resistor using multi-tester</h4><p>(Refer to diagrams showing multi-tester readings for different resistance values)</p>',
                'order' => 1,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet2->id,
                'title' => 'Ohm\'s Law',
                'content' => '<p>Ohm\'s Law states that an electrical circuit\'s current is directly proportional to its voltage, and inversely proportional to its resistance. So, if voltage increases, for example, the current will also increase, and if resistance increases, current decreases; both situations directly influence the efficiency of electrical circuits. To understand Ohm\'s Law, it\'s important to understand the concepts of voltage, and resistance: current is the flow of an electric charge, voltage is the force that drives the current in one direction, and resistance is the opposition of an object to having current pass through it.</p><p>The formula for Ohm\'s Law is:</p><strong>I = V/R       V = I x R       R = V/I</strong><p>where:</p><p>V = voltage in volts</p><p>I = current in amperes</p><p>R = resistance in ohms</p><p>This formula can be used to analyze the voltage, current, and resistance of electricity circuits. Depending on what you are trying to solve we can rearrange it two other ways.</p><p>All of these variations of Ohm\'s Law are mathematically equal to one another. Let\'s look at what Ohm\'s Law tells us. In the first version of the formula, I = V/R, Ohm\'s Law tells us that the electrical current in a circuit can be calculated by dividing the voltage by the resistance. In other words, the current is directly proportional to the voltage and inversely proportional to the resistance. So, an increase in the voltage will increase the current as long as the resistance is held constant. Alternately, if the resistance in a circuit is increased and the voltage does not change, the current will decrease.</p><p>The second version of the formula tells us that the voltage can be calculated if the current and the resistance in a circuit are known. It can be seen from the equation that if either the current or the resistance is increased in the circuit (while the other is unchanged), the voltage will also have to increase.</p><p>The third version of the formula tells us that we can calculate the resistance in a circuit if the voltage and current are known. If the current is held constant, an increase in voltage will result in an increase in resistance. Alternately, an increase in current while holding the voltage constant will result in a decrease in resistance. It should be noted that Ohm\'s law holds true for semiconductors, but for a wide variety of materials (such as metals) the resistance is fixed and does not depend on the amount of current or the amount of voltage.</p><p>As you can see, voltage, current, and resistance are mathematically, as well as, physically related to each other. We cannot deal with electricity without all three of these properties being considered.</p><p>Voltage = 100 volts</p><p>Resistance = 100 ohms(Ω)</p><p>Current = ?</p><p>Answer: 1 ampere</p><p>Formula = I = V/R</p>',
                'order' => 2,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet2->id,
                'title' => 'ELECTRICAL CIRCUITS',
                'content' => '<p>An electrical circuit is a device that uses electricity to perform a task, such as run a vacuum or power a lamp. The circuit is a closed loop formed by a power source, wires, a fuse, a load, and a switch. Electricity flows through the circuit and is delivered to the object it is powering, such as the vacuum motor or light bulb, after which the electricity is sent back to the original source; this return of electricity enables the circuit to keep the electricity current flowing.</p><h4>Types of Circuits</h4><p>A <strong>Series Circuit</strong> is the simplest because it has only one possible path that the electrical current may flow; if the electrical circuit is broken, none of the load devices will work.</p><p>In the <strong>Parallel Circuit,</strong> the electricity can travel to two or more paths. The path in which the electricity travels are separate and if ever one of its path fails to function, the electricity can still flow back to the source through the other paths.</p><p>A <strong>Series-Parallel Circuit,</strong> however, is a combination of the first two. It attaches some of the loads to a series circuit and others to parallel circuits.</p><strong>Series Circuit Formula:</strong><p>R_total = R₁ + R₂ + R₃</p><strong>Parallel Circuit Formula:</strong><p>R_total = 1 / (1/R₁ + 1/R₂ + 1/R₃)</p><strong>Formula for 2 Parallel Circuit only:</strong><p>R_T = (R1 x R2) / (R1 + R2)</p><h4>Series Circuits</h4><p>Series Circuits are the simplest to work with. Here we have three resistors of different resistances. They share a single connection point. When added together the total resistance is 18kΩ.</p><h4>Calculating Total Resistance of a Parallel Circuit</h4><p>R_T = 1 / (1/R1 + 1/R2 + 1/R3 + 1/R4 + 1/R5)</p><p>0.2 + 0.0714 + 0.0476 + 0.04 + 0.01 = 0.369</p><p>R_T = 1/0.369 = 2.7Ω</p><h4>Series-Parallel Circuits</h4><p>Here we can use the shorter Product Over Sum equation as we only have two parallel resistors:</p><p>R_P1 = (R1 x R2) / (R1 + R2) = 37 x 24 / 37 + 24 = 416/61</p><p>R_P1 = 11.0491 + R3 + R₄</p><p>R_T = 11.049 + 58 = 75.0491</p><p>R_T = 7Ω</p><p><em>(Prepare for a Self check and Task Sheet, please provide a sheet of paper as answer sheet)</em></p>',
                'order' => 3,
            ]);


            // ===== Information Sheet 1.3 =====
            $sheet3 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.3'],
                [
                    'title' => 'Capacitors and Diodes',
                    'content' => 'Capacitors and Diodes',
                    'order' => 3,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet3->id)->delete();

            Topic::create([
                'information_sheet_id' => $sheet3->id,
                'title' => 'DEVICES: CAPACITORS',
                'content' => '<p>A <strong>capacitor</strong> is an electronic device or components that store or accumulate electrical energy in a circuit. A capacitor is a tool consisting of two conductive plates, each of which hosts an opposite charge. These plates are separated by a dielectric or other form of insulator, which helps them maintain an electric charge.</p><p><em>Farad</em> – is the measured unit of a capacitance, the higher the farad, the longer it holds the stored charges; the lower the farad, the shorter it holds the stored charges.</p><p>Capacitance is the quantity of electrical energy that a capacitor can store. Capacitance is dependent upon:</p><ul><li>Thickness of dielectric</li><li>Area of the plates</li><li>Constant of dielectric</li></ul><p>The capacitor\'s functions are as follows:</p><p><em>Coupling</em> – to prevent DC from entering the circuit.</p><p><em>De-coupling</em> – to prevent or protect the circuit from another circuit.</p><p><em>Noisefilters or Snubbers</em> – to protect the circuit from distortion and interference.</p><p><em>Motorstarter</em> – is used for starting the motor.</p><p><em>Tuned-in Circuit</em> – is used for tuning circuit.</p><h4>Types of Capacitors</h4><ul><li>Polystyrene (Film capacitor)</li><li>Mylar (for high voltage charge)</li><li>Polyethylene (Film capacitor)</li><li>Mica (for high voltage charge)</li><li>Tantalum (electrolytic type)</li><li>Ceramic (for small amount of charge)</li></ul><p><strong>Electrolytic capacitors</strong> have several prominent characteristics, including:</p><p>a. High capacitance-to-size ratio;</p><p>b. Polarity sensitivity and terminals marked + and -;</p><p>c. Allow more leakage current than other types; and</p><p>d. have their C value and voltage rating printed on them.</p><p>The main advantage of the electrolytic capacitor is the large capacitance-per-size factor. Two obvious disadvantages are the polarity, which must be observed, and the higher leakage current feature.</p><h4>Variable Capacitor</h4><p>A variable capacitor is a special type of capacitor, most commonly used for tuning ratios, which allows the amount of electrical charge it can hold to be altered over a certain range, measured in a unit known as farads. Regular capacitors build up and store an electrical charge until it\'s ready to use. While a variable capacitor stores the charge in the same fashion, it can be adjusted as many times as desired to store different amounts of electricity.</p><p>Two types of variable capacitors include air variable capacitors and vacuum variable capacitors.</p><h4>How to test the capacitor using multi-tester?</h4><strong>Good Capacitor:</strong><p>(Testing electrolytic capacitor: Ohmmeter connection - OHMS, Range - R x 1, Forward Resistance - Low, Capacitance - depends on value, Condition - Good)</p><strong>Shorted Capacitor:</strong><p>(Testing electrolytic capacitor: Range - R x 1, Forward Resistance - Zero, Condition - Shorted)</p><strong>Open Capacitor:</strong><p>(Testing electrolytic capacitor: Range - R x 10, Forward Resistance - Infinite, Capacitance - 470uF, Condition - Open)</p><strong>Leaky Capacitor:</strong><p>(Testing electrolytic capacitor: Range - R x 10, Forward Resistance - Zero, Capacitance - 100 uF, Condition - Leaky)</p>',
                'order' => 1,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet3->id,
                'title' => 'DEVICES: DIODE',
                'content' => '<p>A diode is a semi-conductor device that permits the flow of current in only one direction. A Diode can convert electric current from AC to DC or from Alternating Current to Direct Current. This is called Rectification, and rectifier diodes are most commonly used in low current power supplies.</p><p>Rectification is a process of converting AC to DC.</p><p>A diode has 2 parts:</p><p>Anode (A) +       Cathode (K) -</p><h4>TYPES OF DIODE</h4><strong>1. Rectifier Diode - Converts AC to DC.</strong><strong>2. Signal Diode</strong><p>Both diodes work the same way by allowing current to flow in one direction. The differences have to do with power and frequency characteristics. They are made from a p-n junction and are two lead devices.</p><p>Small signal diodes have much lower power and current ratings, around 150mA, 500mW maximum compared to rectifier diodes, they can also function better in high frequency applications or in clipping and switching applications with short-duration pulse waveforms.</p><p>- converts Intermediate Frequency (IF) to Audio Frequency (AF).</p><strong>3. Regulator Diode - used for voltage regulation.</strong><strong>4. Temperature Dependent Diode - used for temperatures hot or cold to automatically on and off.</strong><strong>5. Light Emitting Diode (L.E.D)</strong><p>is a semiconductor light source. LEDs are used as indicator lamps in many devices and are increasingly used for other lighting. Photodiode or Photo Sensitive Diode - Allows current flow when exposed to light (vice versa).</p><strong>6. Photodiode (Photosensitive diode)</strong><p>is a type of photodetector capable of converting light into either current or voltage, depending upon the mode of operation. The common, traditional solar cell used to generate electric solar power is a large area photodiode.</p><h4>How to test the Diode using multi-tester?</h4><strong>Good Diode:</strong><p>(Ohmmeter connection: Range R x 10, Forward Resistance - LOW (Not zero/not infinite), Part No. - 1N4001, Reverse: Range R x 10, Reverse Resistance - Infinite, Condition - Good)</p><strong>Shorted Diode:</strong><p>(Range R x 1, Forward Resistance - Zero, Reverse: Range R x 1, Reverse Resistance - Zero, Condition - Shorted)</p><strong>Open Diode:</strong><p>(Range R x 10, Forward Resistance - LOW (Not zero/not infinite), Part No. - 1N2Rs, Reverse: Range R x 10, Reverse Resistance - Infinite/zero, Condition - Open)</p><strong>Leaky Diode:</strong><p>(Range R x 1, Forward Resistance - Zero, Reverse: Range R x 1, Reverse Resistance - Zero, Condition - Shorted)</p><p><em>(Prepare for a Self check and Task Sheet, please provide a sheet of paper as answer sheet)</em></p>',
                'order' => 2,
            ]);


            // ===== Information Sheet 1.4 =====
            $sheet4 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.4'],
                [
                    'title' => 'Transistors, Integrated Circuits (ICs) and Transformers',
                    'content' => 'Transistors, Integrated Circuits (ICs) and Transformers',
                    'order' => 4,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet4->id)->delete();

            Topic::create([
                'information_sheet_id' => $sheet4->id,
                'title' => 'DEVICES: TRANSISTOR',
                'content' => '<p>It is an electronic semi-device which provides <em>oscillation, amplification, switching</em> and <em>rectification</em> of electrical current. The principal materials used are germanium and silicon. Basically, there are two kinds of transistors, namely:</p><p><em>Oscillation</em> - A process of moving back and forth, to and from.</p><p><em>Amplification</em> - A process of converting.</p><p>1. Positive Negative Positive (PNP)</p><p>2. Negative Positive Negative (NPN)</p><p>A transistor is an electronic amplifying device with 2 junction type. Transistors are the NPN and PNP impurity materials utilized to determine the conductivity type of a semi-conductor.</p><p>Transistors are composed of three parts – a base, a collector, and an emitter. The base is the gate controller device for the larger electrical supply. The collector is the larger electrical input, and the emitter is the outlet for that supply.</p><p>B – Base – Input</p><p>C – Collector – output</p><p>E – Emitter – Ground</p><h4>SILICON CONTROLLED RECTIFIER</h4><p>A <strong>silicon-controlled rectifier (or semiconductor-controlled rectifier)</strong> is a four-layer solid state current controlling device. The name \\ or <strong>SCR</strong> is General Electric\'s trade name for a type of thyristor.</p><p>SCRs are unidirectional devices (i.e. can conduct current only in one direction) as opposed to TRIACs which are bidirectional (i.e. current can flow through them in either direction). SCRs can be triggered normally only by currents going into the gate as opposed to TRIACs which can be triggered normally by either a positive or a negative current applied to its gate electrode.</p>',
                'order' => 1,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet4->id,
                'title' => 'DEVICES: INTEGRATED CIRCUIT (IC)',
                'content' => '<p>Integrated Circuits (ICs) are used in all types of modern electronic devices. They are integrated, meaning that they are made as a total circuit and housed in one enclosure. The enclosure may take a number of shapes, it may be similar to a 2-5 transistor package with 8 leads instead of 3, it may be what is referred to as a dual in-line package (DIP) with as many as 24 leads. All components are manufactured as a common unit.</p><p>There are two major kinds of ICs:</p><p>1. Analog (or linear) which are used as amplifiers, timers and oscillators</p><p>2. Digital (or logic) which is used in microprocessors and memories</p><p>Some ICs are combinations of both analog and digital.</p><p>There are 3 categories of IC packages:</p><p>1. Small scale integration (SSI)</p><p>2. Medium scale Integration (MSI)</p><p>3. Large scale integration.</p><p>The SSI package generally has fewer than 200 components in it, MSI and LSI package may have anywhere from 1000 to 256,000 or more components.</p><p>Keep in mind that transistors, diodes, and resistors and capacitors are referred to as discrete components, and an IC may have thousands of these discrete components located on one chip. The concept of having thousand of part on one chip so small that the human eye cannot see is hard to believe when you have been working with vacuum tubes and transistor to increase this number of components on a chip.</p><p>ICs can be used to do any number of things electronically; they are classified further according to their function. The two broad categories of classification here are digital and linear.</p><strong>Dual In-line Package (DIP) IC</strong><p>This is an electronic device package with a rectangular housing and two parallel rows of electrical connecting pins. The package may be through-hole mounted to a printed circuit board or inserted in a socket.</p><strong>Linear IC</strong><p>This is a solid-state analog device characterized by a theoretically infinite number of possible operating states. It operates over a continuous range of input levels. In contrast, a digital IC has a finite number of discrete input and output states.</p><strong>Ball Grid Array (BGA) IC</strong><p>This is a type of surface-mount packaging used for integrated circuits. Ball-grid array (BGA) packages are used to permanently mount devices such as microprocessors.</p><strong>Surface-Mounted Device (SMD) IC</strong><p>This is a method for constructing electronic circuits in which the components are mounted directly onto the surface of printed circuit boards (PCBs). An electronic device so made is called a surface-mount device (SMD).</p>',
                'order' => 2,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet4->id,
                'title' => 'DEVICES: TRANSFORMERS & POWERSUPPLY',
                'content' => '<h4>Types of Power Supply</h4><p>There are many types of power supply. Most are designed to convert high voltage AC mains electricity to a suitable low voltage supply for electronic circuits and other devices. A power supply can be broken down into a series of blocks, each of which performs a particular function.</p><p>For example a full regulated supply:</p><strong>Block Diagram of a Regulated Power Supply System:</strong><p>220V AC Mains → Transformer → Rectifier → Smoothing → Regulator → Regulated 5V DC</p><p>Each of the blocks is described in more detail below:</p><p><em>Transformer</em> - steps down high voltage AC mains to low voltage AC.</p><p><em>Rectifier</em> - converts AC to DC, but the DC output is varying.</p><p><em>Smoothing</em> - smooths the DC from varying greatly to a small ripple.</p><p><em>Regulator</em> - eliminates ripple by setting DC output to a fixed voltage.</p><p>Power supplies made from these blocks are described below with a circuit diagram and a graph of their output:</p><ul><li>Transformer only</li><li>Transformer + Rectifier</li><li>Transformer + Rectifier + Smoothing</li><li>Transformer + Rectifier + Smoothing + Regulator</li></ul><strong>Transformer only</strong><p>Input: high voltage AC (mains supply) → Output: low voltage AC</p><p>The low voltage AC output is suitable for lamps, heaters and special AC motors. It is not suitable for electronic circuits unless they include a rectifier and a smoothing capacitor.</p><strong>Transformer + Rectifier</strong><p>Output: varying DC</p><p>The varying DC output is suitable for lamps, heaters and standard motors; it is not suitable for electronic circuits unless they include a smoothing capacitor.</p><strong>Transformer + Rectifier + Smoothing</strong><p>Output: smooth DC</p><p>The smooth DC output has a small ripple. It is suitable for most electronic circuits.</p><strong>Transformer + Rectifier + Smoothing + Regulator</strong><p>Output: regulated DC</p><p>The regulated DC output is very smooth with no ripple. It is suitable for all electronic circuits.</p><strong>Dual Supplies</strong><p>Some electronic circuits require a power supply with positive and negative outputs as well as zero volts (0V). This is called a dual supply because it is like two ordinary supplies connected together as shown in the diagram.</p><p>Dual supplies have three outputs, for example a 15V supply has +9V, 0V and -9V outputs.</p><h4>Transformer</h4><p>Transformers convert AC electricity from one voltage to another with little loss of power. Transformers work only with AC and this is one of the reasons why mains electricity is AC.</p><p>Step up transformers increase voltage, step-down transformers reduce voltage. Most power supplies use a step-down transformer to reduce the dangerously high mains voltage (220V) to a safer low voltage.</p><strong>3 Parts of Transformer:</strong><p>Core, Primary, Secondary</p><p>The input coil is called the primary and the output coil is called the secondary. There is no electrical connection between the two coils, instead they are linked by an alternating magnetic field created in the soft iron core of the transformer. The two lines in the middle of the circuit symbol represent the core.</p><p>Transformers waste very little power so the power out is (almost) equal to the power in. Note that as voltage is stepped down current is stepped up.</p><p>The ratio of the number of turns on each coil, called the turn\'s ratio, determines the ratio of the voltages. A step-down transformer has a large number of turns on its primary (input) coil which is connected to the high voltage mains supply, and a small number of turns on its secondary (output) coil to give a low output voltage.</p><strong>Kinds of transformer:</strong><p>1. Power Transformer</p><p>2. Isolation Transformer</p><p>3. Auto Transformer</p><p>4. Audio Transformer</p><p>5. RF and IF Transformer</p><strong>Types of Power Transformer:</strong><ul><li>Center Tap Transformer</li><li>Multi Tap Transformer</li></ul><h4>Rectifier</h4><p>There are several ways of connecting diodes to make a rectifier to convert AC to DC. A full-wave rectifier, the bridge rectifier is the most important and it produces full-wave varying DC. But this method is costly. It can also be made from just two diodes if a center-tap transformer is used. A single diode can be used as a rectifier but it only produces half-varying DC.</p><strong>Bridge rectifier</strong><p>A bridge rectifier can be made using four individual diodes, but it is also available in special packages containing the four diodes required. It is called a full-wave rectifier because it uses all the AC wave (both positive and negative sections). 1.4V is used up in the bridge rectifier because each diode uses 0.7V when conducting and there are always two diodes conducting, as shown in the diagram below.</p><p>Bridge rectifier: Alternate pairs of diodes conduct, changing over the connections so the alternating directions of AC are converted to the one direction of DC.</p><p>Output: full-wave varying DC (using all the AC wave)</p><strong>Single diode rectifier</strong><p>A single diode can be used as a rectifier but this produces half-wave varying DC which has gaps when the AC is negative. It is hard to smooth this sufficiently well to supply electronic circuits unless they require a very small current so the smoothing capacitor does not significantly discharge during the gaps.</p><p>Output: half-wave varying DC (using only half the AC wave)</p><h4>Smoothing</h4><p>Smoothing is performed by a large value <strong>electrolytic capacitor</strong> connected across the DC supply to act as a reservoir, supplying current to the output when the varying DC voltage from the rectifier is falling. The diagram shows the unsmoothed varying DC (dotted line) and the smoothed DC (solid line).</p><p>The capacitor charges quickly near the peak of the varying DC, and then discharges as it supplies current to the output.</p><p>Smoothing is not perfect due to the capacitor voltage falling a little as it discharges, giving a small ripple voltage. For many circuits a ripple which is 10% of the supply voltage is satisfactory and the equation below gives the required value for the smoothing capacitor. A larger capacitor will give fewer ripples. The capacitor value must be doubled when smoothing half-wave DC.</p><h4>Regulator</h4><p>Voltage regulator ICs are available with fixed (usually 5, 12 and 15V) or variable output voltages. They are also rated by the maximum current they can pass. Negative voltage regulators are available, mainly for use in dual supplies. Most regulators include some automatic protection from excessive current (\'overload protection\') and overheating (\'thermal protection\').</p><p>Many of the fixed voltage regulator ICs has 3 leads and look like power transistors, such as the 7805 +5V 1A regulator shown on the right. They include a hole for attaching a heatsink if necessary.</p><p><em>(Prepare for a Self check and Task Sheet, please provide a sheet of paper as answer sheet)</em></p>',
                'order' => 3,
            ]);


            // ===== Information Sheet 1.5 =====
            $sheet5 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.5'],
                [
                    'title' => 'Schematic Diagram, Pictorial Diagram, Block Diagram and PCB Making',
                    'content' => 'Schematic Diagram, Pictorial Diagram, Block Diagram and PCB Making',
                    'order' => 5,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet5->id)->delete();

            Topic::create([
                'information_sheet_id' => $sheet5->id,
                'title' => 'SCHEMATIC DIAGRAM',
                'content' => '<p>A drawing showing all significant components, parts, or tasks (and their interconnections) of a circuit, device, flow, process, or project by means of standard symbols.</p><p>A connection of Resistors in series and parallel is also a schematic diagram, below is a sample of complicated diagrams where capacitors, and diodes are added in series – parallel connections.</p>',
                'order' => 1,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet5->id,
                'title' => 'PICTORIAL DIAGRAM',
                'content' => '<p>A simplified diagram which shows the various components of a system (motorcycle, car, ship, electronic devices, airplane, etc) without regard to their physical location, how the wiring is marked, or how the wiring is routed. It does, however, show you the sequence in which the components are connected.</p>',
                'order' => 2,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet5->id,
                'title' => 'SAMPLE OF PICTORIAL AND SCHEMATIC SYMBOL',
                'content' => '<h4>ADDITIONAL SCHEMATIC AND PICTORIAL SYMBOLS</h4><p>Capacitor-Non Polarized</p><p>Capacitor-Polarized</p><p>Resistor</p><p>Variable Resistor</p><p>Diode</p><p>Light Emitting Diode (LED)</p><p>Symbols for Ground</p><h4>SCHEMATIC DIAGRAM OF FLIP-FLOP</h4><p>Components: 470R resistors (x2), 10k resistors (x2), 100u capacitors (x2), LED1 (Red LED), LED2 (Green LED), Q1 BC 547, Q2 BC 547, Sw (switch), 9v battery</p><h4>SCHEMATIC DIAGRAM OF FULL-WAVE BRIDGE TYPE MULTI TAP TRANSFORMER</h4><p>220V input with selector switch, multiple voltage taps (3v, 4.5v, 6v, 9v, 12v), bridge rectifier, and output connections.</p><p>Selector Switch / 6 Way Rotary</p><p>D1-D4: 4004/06</p><p>R1-R3: 1K ohms</p><p>F: 1000uf/35volts</p>',
                'order' => 3,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet5->id,
                'title' => 'PRINTED CIRCUIT BOARD MAKING',
                'content' => '<strong>Step 1: Materials</strong><p>1. Copper Clad (2x2)</p><p>2. Masking Tape</p><p>3. Ruler</p><p>4. Knife Cutter</p><p>5. Pencil</p><p>6. Mini Drill</p><p>7. Ferric Chloride</p><p>8. Sand Paper</p><p>9. Plastic Container</p><strong>Step 2: Designing the circuit</strong><p>Using paper and pencil design the layout of the circuit, it is easiest to do this as a top view of the board, it helps to also have all the different components on hand to help with spacing and placement. As a side note also make sure to design the layout so that it will fit on the board. If you already have a pre designed layout you can skip this part.</p><strong>Step 3: Drawing the traces</strong><p>Next you will want to make a copy of the design that is a reverse of the original, if you drew it in reverse or the one you have is already reversed just make a regular copy of it. Cut the copy of the layout out with scissors leaving some on either side so you can fold it around the PCB and tape it in place. Now using the tape, tape the design onto the copper side of the PCB. With the #65 drill bit use the layout to drill a hole in the center of all the solder pads for the individual components.</p><strong>Step 4: Cutting the Excess</strong><p>-Gently glide the knife along the edge of your desired PCB design.</p><p>-Strip the excess masking tape to reveal the design of your work</p><p>-Try not to puncture the design to prevent damage to the connection that will affect the functionality of your circuit during the etching process</p><strong>Step 5: Etching</strong><p>-Start off by finding a clean dry place where you can safely etch the circuit board, preferably outside.</p><p>-Drop PCB into the Ferric Chloride, copper side up and place the small container into the water in the larger container.</p><p>-Gently rock the small container in the water in so to keep the FC moving which helps with the etching process.</p><p>-in about 5-7 minutes you should start to see the copper start to dissolve away, notice the areas where the traces a drawn are unaffected.</p><p>-After about 10-12 minutes the board should be completely etched, at which time you should immediately remove the PCB and drop it into the water in the larger container to rinse it and then dry it off on the paper towel.</p><p>-When you are done put the cover on the small container, you can use the Ferric Chloride over again a few times, and pour out the water in the larger container and rinse it out. You can use the larger container to store the small container and your extra Ferric Chloride that is still in the original bottle.</p><strong>Step 6: Cleaning the PCB</strong><p>-using 1000 sand paper clean the Sharpie off the traces.</p><p><em>(Prepare for a Self check and Task Sheet, please provide a sheet of paper as answer sheet)</em></p>',
                'order' => 4,
            ]);


            // ===== Information Sheet 1.6 =====
            $sheet6 = InformationSheet::updateOrCreate(
                ['module_id' => $module->id, 'sheet_number' => '1.6'],
                [
                    'title' => 'Soldering and De-soldering, Terminaling and Connecting, Troubleshooting Process',
                    'content' => 'Soldering and De-soldering, Terminaling and Connecting, Troubleshooting Process',
                    'order' => 6,
                ]
            );

            // Delete old topics for this sheet
            Topic::where('information_sheet_id', $sheet6->id)->delete();

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'SOLDERING PROCESS',
                'content' => '<h4>SOLDERING IS EASY - HERE\'S HOW TO DO IT</h4><p>Step-by-step soldering guide:</p><p>1. Get your tools ready: soldering iron, solder, wet sponge</p><p>2. Place the tip of the iron on the joint</p><p>3. Feed solder to the joint (not the iron)</p><p>4. Remove the solder, then the iron</p><p>5. Inspect the joint - it should be shiny and smooth</p><p>Tips: Clean the tip regularly on a wet sponge. Heat both parts of the joint. Apply solder to the joint, not the iron. Use the right amount of solder. Keep the iron tip clean and tinned.</p>',
                'order' => 1,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'What is Soldering and Desoldering?',
                'content' => '<p>Soldering is a process in which two or more items are joined together by melting and putting a filler metal (solder) into the joint, the filler metal having a lower melting point than the adjoining metal. Soldering differs from welding in that soldering does not involve melting the work pieces. In brazing, the work piece metal also does not melt, but the filler metal is one that melts at a higher temperature than in soldering. In the past, nearly all solders contained lead, but environmental and health concerns have increasingly dictated use of lead-free alloys for electronics and plumbing purposes.</p><p>Soldering is a process of connecting/joining two metallic surfaces (e.g. terminals of components and the PCB copper pads) with the use of a soldering iron and a solder lead. This process is commonly used in electronics for permanent electrical connections between electronic components/parts on a Printed Circuit Board (PCB). There are three types of soldering that are commonly used:</p><p>1. Soft soldering</p><p>2. Hard soldering (silver soldering and brazing)</p><p>3. Braze welding</p><p>Soft soldering, which uses tin/lead alloy as its filler metal, is the most widely used for making connections in the electronics field. Solder paste is used then types of soldering to be selected to modify the melting point.</p><p>Not all soldering processes produce the same results; there are many types of soldering to be selected for which type the correct soldering should be done.</p>',
                'order' => 2,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'SOLDERING AND DESOLDERING TOOLS',
                'content' => '<strong>A soldering iron</strong><p>is a hand tool used in soldering. It supplies heat to melt solder so that it can flow into the joint between two workpieces. A soldering iron is composed of a heated metal tip and an insulated handle. For home-based electronics work use a soldering iron in the range of 15W to 30W for best results. It would be preferable that the tip is a fine conical type for precision soldering work, in electronic assembly.</p><strong>A soldering gun</strong><p>is an approximately pistol-shaped, electrically powered tool for soldering metals using tin-based solder to achieve a strong mechanical bond with good electrical contact. The body of the tool contains a transformer with a primary winding connected to mains electricity through a trigger switch in the handle, and a single-turn secondary winding of thick copper with very low resistance. Pressing the trigger causes a current to flow through the copper tip, which is resistively heated.</p><strong>Tweezers (Pliers)</strong><p>are tools used for picking up objects too small to be easily handled with the human fingers.</p><strong>A Solder sucker</strong><p>is a device used to remove solder from a printed circuit board (PCB). Its purpose is to aid in desoldering, the process of removing components from the board via the application of heat and removal of solder from the connection. It is usually a manually operated vacuum pump, which can be used to remove molten solder, or it can be the de-solder wick, usually a braided copper wire which uses capillary action to remove the solder from previously soldered connection.</p>',
                'order' => 3,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'SOLDERING SUPPLIES AND MATERIALS',
                'content' => '<p>Due to the extremely small size of modern electronic components, it is sometimes necessary to use a magnifying glass or binocular microscope during the process of hand soldering. Excessive application of heat can damage sensitive components. While the PCB itself and the immediate surrounding area should not heat up significantly during the soldering process, overheating can destroy parts and loosen copper foils. Choosing the right type of solder for each situation is important for making durable connections.</p><strong>Solder</strong><p>is a fusible metal alloy used to create a permanent bond between metal workpieces. Solder is melted in order to adhere to and connect the pieces after cooling, which requires that an alloy suitable for use as solder have a lower melting point than the pieces being joined. The solder should also be resistant to oxidative and corrosive effects that would degrade the joint over time. Solder used in making electrical connections also needs to have favorable electrical characteristics.</p><p><strong>Tin-lead solders,</strong> also called soft solders, are commercially available with tin concentrations between 5% and 70% by weight. The greater the tin concentration, the greater the solder\'s tensile and shear strengths. For electrical and electronic work, 60/40 (Sn/Pb) solder is principally used for electrical/electronic soldering.</p><p>Due to health concerns associated with lead, the manufacture and use of lead-based solders is being eliminated. Significant lead-free solders include tin, copper, silver, bismuth, indium, zinc, antimony, and traces of other metals in varying amounts. Contact at the melting points from 5 to 20 °C higher, though solder.</p><strong>Flux</strong><p>is a reducing agent designed to help reduce (return oxidized metals to their metallic state) metal oxides at the points of contact to improve the electrical connection and mechanical strength. Two principal types of flux are acid flux, used for metal mending and plumbing, and rosin flux, used in electronics and plumbing; rosin flux is preferred in electronics since acid flux is corrosive and can damage delicate circuitry.</p><p><em>Solder paste</em> (or solder cream) is used to connect the leads of integrated circuits and other components to a circuit board in surface mount technology. The paste consists of a flux and tiny spheres of solder.</p><p><em>Solder wick</em> or desoldering braid is a pre-fluxed copper braid used to remove solder. It consists of braided strands of copper wire coated with flux. A typical use is in the removal of excess solder, to correct a solder bridge, or to desolder a connection so that a component can be replaced. The braid may also be used without flux, but it is less efficient and more likely to leave small balls of solder on the board. The flux in the solder braid will flow into the molten solder.</p><p>Solder paste (or solder cream) is used to connect the leads of IC and other surface mounted components to the PCB. Soldering paste is a pre-mixed blend of small spheres of solder combined with flux.</p>',
                'order' => 4,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'SMD SOLDERING',
                'content' => '<p>Surface mount components, at the time this article is written, are by far the most commonly used components in the market. With the continuous trend of miniaturization SMDs are available in Packages smaller than 0.4 x 0.2 mm. Reflowed Solder at SMDs.</p>',
                'order' => 5,
            ]);

            Topic::create([
                'information_sheet_id' => $sheet6->id,
                'title' => 'SOLDERING DEFECTS',
                'content' => '<strong>Common defects:</strong><ul><li>Cold solder joint: caused when the solder cools too quickly or parts move during cooling. It has a dull, grainy appearance. A dry joint is weak mechanically and a poor conductor.</li><li>Disturbed joint: caused from touching the solder before it is set. Similar to a cold joint, but less severe.</li><li>Overheating: caused by leaving the iron on the joint for too long, which can damage components.</li><li>Insufficient wetting: when solder doesn\'t flow properly. Results in poor connection.</li><li>Solder bridge: unwanted solder connecting adjacent tracks or pads on PCB.</li></ul><p>A dry joint appears as a rough, uneven surface that has a lumpy, crystallized texture. It has been caused by moving the joint while the solder was still liquid, or by the iron being taken off the joint too soon, or by putting too little solder on the joint.</p><p>In practice, it has been found that adding solder to correct a joint gives poor results, because the fresh solder melts at a lower temperature than the contaminated joint. A dry joint is weak mechanically and a poor conductor.</p><h4>GOOD JOINT vs BAD JOINT</h4><p>A bad joint looks dull, has lumps, has air pockets or voids, or has a weak or cold solder appearance.</p><p>In electronics, a \'veroboard\' or \'stripboard\' is used then more boards (matrix/PCB); a perfboard (proto board), is a material for prototyping of electronic circuits. It is made of thin, rigid copper-clad or the copper track (strip off). Excessive heat or force may pull off the copper from the board, particularly on single sided PCBs without through-hole plating.</p><strong>Here are some helpful tips to help you solder:</strong><p>a) good, all surfaces wet with solder</p><p>b) too little solder</p><p>c) too much solder, may hide a bad solder joint/no connection</p><p>d) pad, etch or surface not soldered properly, the solder has not wetted the surfaces</p><p>e) solder bridge, may cause a short circuit on close tracks</p>',
                'order' => 6,
            ]);


            DB::commit();
            $this->command->info('Information Sheets 1.1-1.6 with topics and parts seeded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Seeder failed: ' . $e->getMessage());
            throw $e;
        }
    }
}

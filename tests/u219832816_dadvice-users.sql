-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 22, 2025 at 05:36 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u219832816_dadvice`
--

-- --------------------------------------------------------

--
-- Table structure for table `age_groups`
--

CREATE TABLE `age_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `age_groups`
--

INSERT INTO `age_groups` (`id`, `name`, `display_name`, `sort_order`) VALUES
(1, 'newborn', 'Newborn (0-3 months)', 1),
(2, 'baby', 'Baby (3-12 months)', 2),
(3, 'toddler', 'Toddler (1-3 years)', 3),
(4, 'preschooler', 'Preschooler (3-5 years)', 4),
(5, 'school-age', 'School Age (5-11 years)', 5),
(6, 'tween', 'Tween (11-13 years)', 6),
(7, 'teenager', 'Teenager (13-18 years)', 7),
(8, 'young-adult', 'Young Adult (18+ years)', 8),
(9, 'all-ages', 'All Ages', 9);

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` varchar(255) NOT NULL,
  `title` varchar(500) NOT NULL,
  `summary` text DEFAULT NULL,
  `full_content` longtext DEFAULT NULL,
  `content_type` enum('heartwarming-story','practical-tip','expert-technique','real-life-hack','research-insight','problem-solution','funny-moment','aha-moment') NOT NULL,
  `urgency` enum('timeless','trending','seasonal','urgent') DEFAULT 'timeless',
  `engagement_score` int(11) DEFAULT 0,
  `practical_score` int(11) DEFAULT 0,
  `universal_appeal` int(11) DEFAULT 0,
  `quote_highlight` text DEFAULT NULL,
  `newsletter_priority` int(11) DEFAULT 0,
  `app_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `view_count` int(11) DEFAULT 0,
  `bookmark_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `summary`, `full_content`, `content_type`, `urgency`, `engagement_score`, `practical_score`, `universal_appeal`, `quote_highlight`, `newsletter_priority`, `app_featured`, `created_at`, `updated_at`, `view_count`, `bookmark_count`) VALUES
('CARE_FEEDING_ONLY_CHILD', 'The Only Child Dilemma: What to Do When They Beg for a Sibling', 'That heart-sinking moment when your only child asks for a brother or sister you know you can\'t give them. It\'s a tricky one. The advice here is wonderfully reassuring: focus on building a \'chosen family\' and fostering deep, meaningful connections outside the home. It’s about community, not just biology.', 'A listener with a five-year-old only child wrote in with a common and difficult problem: her daughter has started asking for a sibling. For this family, having another child isn\'t in the cards, leaving them wondering how to fulfil their daughter\'s need for connection.\n\nThe hosts offer some truly excellent advice that shifts the focus from what\'s missing to what can be built:\n\n1.  **Embrace the \'Chosen Family\':** The concept of a \'chosen family\' is key. These are the friends and community members who become as close as blood relatives. It\'s about consciously fostering these deep connections. You don\'t just need more playdates; you need consistent, reliable relationships where your child feels a sense of belonging with other families.\n\n2.  **Be Intentional:** Since you don\'t have the built-in playmate of a sibling, you have to be more intentional. This means actively seeking out community. Ideas include:\n    *   **Regular Rituals:** Set up regular, predictable get-togethers with another family you click with. Make it a standing pizza night or a Sunday park visit.\n    *   **Deepen School Friendships:** Go beyond casual playground chats. If you find a parent you like, be upfront: \"We\'re really looking to make some deeper friendships. Would you be open to getting together more regularly?\"\n    *   **Shared Activities:** Join a church, a local club, or a sports team where you see the same families week after week. These shared experiences are the bedrock of strong community.\n\n3.  **Validate, Don\'t Dismiss:** Acknowledge your child\'s feelings. It\'s okay for them to be sad or want a sibling. You can say, \"I know you\'d love a brother or sister, and it\'s okay to feel that way. Our family is special just as it is, and we have lots of wonderful people in our lives to be our family too.\"\n\nUltimately, the goal isn\'t to replace a sibling, but to show your child that family and deep connection come in many forms. It\'s a beautiful opportunity to teach them about the power of community.', 'problem-solution', 'timeless', 8, 9, 8, 'I want to say that chosen family can be just as lovely and just as connected as blood relatives.', 2, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('DLCOD_IChanneledMyAngerIntoArt_001', 'How Shrinky Dinks Healed a Mother\'s Rage and Transformed Her Family Life', 'A mother, struggling with deep-seated anger from her childhood, followed a piece of advice to channel her feelings into art. Her choice? The humble Shrinky Dink. The result was a remarkable transformation, not just in her mood, but in every aspect of her family life. It\'s a rather brilliant reminder that sometimes the simplest creative acts can have the most profound impact.', 'A caller named Bridget shared a powerful story about taking Dr. Laura\'s advice to heart. Haunted by rage from a difficult childhood, she was finding it hard to maintain positive habits and goals. The advice she heard on a previous show was to channel that intense emotion into art. In a move of simple genius, she chose Shrinky Dinks.\n\nThis small, seemingly childish creative outlet became a catalyst for massive change. Bridget explained that before this, she struggled to be consistent with anything. After starting her simple craft project, a cascade of positive changes followed:\n\n- She established a regular routine of prayer and scripture study.\n- She began exercising, taking daily walks with her husband.\n- Her house became cleaner and more organised.\n- She significantly reduced her screen time, leading to more quality connection with her husband and children.\n- She felt calmer, more at peace, and even started exploring other hobbies like embroidery.\n\nIt\'s a wonderful testament to the idea that you don\'t need a grand, expensive new hobby to make a difference. Sometimes, reconnecting with a simple, joyful activity is all it takes to start a positive ripple effect through your entire life and family.', 'heartwarming-story', 'timeless', 9, 8, 9, 'And it really, um, it was really helping me... my house is a lot cleaner. I spend less time on my screens and more time connecting with my husband and my children.', 1, 1, '2025-07-22 15:22:53', '2025-07-22 15:22:53', 0, 0),
('DLCOD_IChanneledMyAngerIntoArt_002', 'Are You Selfish for Prioritising Your Own Family? (Spoiler: No, You\'re Not)', 'After a burst of self-improvement, a mother felt immense guilt for no longer wanting to spend her free time helping her own ageing mother. Dr. Laura\'s reframe is a game-changer for anyone feeling stretched thin by family obligations. It’s not about being selfish; it’s about wisely investing your limited time and energy where it matters most: your own home.', 'The caller, Bridget, found that as she channelled her anger into art and began improving her own life, she lost the desire to help her mother, who struggles with depression. Previously, she\'d spent one day a week doing her mother\'s cleaning and yard work. Now, she felt guilty, wondering if she had become a \'selfish bitch\'.\n\nDr. Laura immediately offered a powerful new perspective. She pointed out that Bridget hadn\'t simply stopped helping; she had reallocated her time and energy. The hours once spent at her mother\'s house were now being invested in her own family – her children, her husband, and her home.\n\n\"That was the time you gave to your mother,\" Dr. Laura explained. \"Now you\'re giving it to your house, your kids, and your husband.\" This isn\'t a moral failing; it\'s a necessary re-prioritisation. There are only so many hours in the day and so much energy to go around. When you have your own children and partner, they become your primary responsibility. Choosing to focus your energy on your own nuclear family isn\'t selfish; it\'s the very definition of being a responsible and present parent and partner.', 'problem-solution', 'timeless', 9, 9, 10, 'That was the time you gave to your mother. Now you\'re giving it to your house, your kids, and your husband... It\'s not a matter of becoming a selfish bitch. There\'re just so many hours and energy in a day.', 2, 1, '2025-07-22 15:22:53', '2025-07-22 15:22:53', 0, 0),
('DLCOD_IChanneledMyAngerIntoArt_003', 'The 10-Second Trick for When \'Boundary Guilt\' Strikes', 'You\'ve done the hard work of setting a healthy boundary, but now the guilt is creeping in. What to do? Dr. Laura’s advice is pure gold and ridiculously simple. Redirect that feeling immediately into the positive habit that’s giving you strength. Feeling sad about Mum? Grab a Shrinky Dink. It’s about replacing a destructive thought pattern with a creative, healing action.', 'After establishing that it was healthy for her to step back from her mother\'s care, the caller, Bridget, asked a crucial question: \"When my sadness comes up, what should I do?\" Dr. Laura\'s response was instant, direct, and brilliant: \"Shrinky Dinks. Grab a Shrinky Dink. I\'m serious. Keep them around.\"\n\nThis simple instruction is a powerful technique for managing the inevitable feelings of guilt or sadness that can arise after setting a necessary boundary with a family member. The strategy is to immediately redirect the negative emotion into the very activity that is helping you heal and build a better life for yourself.\n\nInstead of dwelling on the guilt, which is unproductive and draining, you actively engage in the positive, self-caring habit you\'ve established. For Bridget, that\'s her art. For you, it could be:\n\n-   Putting on your running shoes and going for a quick jog around the block.\n-   Picking up the knitting needles for just a few rows.\n-   Spending five minutes in the garden.\n-   Playing a quick song on the piano.\n\nBy physically replacing the old, draining habit of worrying and feeling guilty with a new, fulfilling action, you are retraining your brain and reinforcing the positive change you\'ve made. It\'s a real-life hack for managing difficult emotions.', 'practical-tip', 'timeless', 9, 10, 10, 'When my sadness comes up... what should I do? ... Shrinky dinks. Grab a shrinky dink. I\'m serious. Keep them around.', 1, 1, '2025-07-22 15:22:53', '2025-07-22 15:22:53', 0, 0),
('DLCOD_IChanneledMyAngerIntoArt_004', 'The \'Shrinky Dink Test\' for Helping Struggling Family Members', 'It\'s one thing to help a family member who\'s hit a rough patch, but quite another to prop up someone who won\'t help themselves. Dr. Laura offers a sharp insight: did you take action when you were struggling? If so, perhaps the best \"help\" you can offer isn\'t doing their chores, but inspiring them to find their own \"Shrinky Dinks\"—their own small step toward change. It\'s the difference between helping and enabling.', 'The caller, Bridget, felt compelled to help her mother because she saw her mother struggling with depression and sadness. Dr. Laura drew a critical line between helping someone through a tough time and taking responsibility for someone who isn\'t taking action for themselves. The key question came when Dr. Laura connected Bridget\'s own past depression with her mother\'s ongoing struggles.\n\nDr. Laura pointed out, \"You were depressed, and then you did your Shrinky Dinks. You did something about your depression.\" This created what could be called the \'Shrinky Dink Test\'. When faced with helping a struggling family member, ask yourself:\n\n1.  **Are they struggling with circumstances beyond their control, or is there a component of inaction?** Dr. Laura asks if the mother\'s struggle is attitude, health, or both. This helps separate what can be changed from what cannot.\n\n2.  **Are they taking any small steps to help themselves?** Bridget took the small step of starting a craft, which led to bigger changes. If the person you\'re helping isn\'t taking any steps, your \'help\' may be enabling their inaction.\n\n3.  **Is your \'help\' actually helping, or is it just making you feel less guilty?** Bridget admitted she didn\'t actually *like* spending time with her mother. The motivation was pity. This kind of \'help\' often creates resentment and burnout without fostering any real change in the other person.\n\nDr. Laura\'s cheeky suggestion to \"get her some Shrinky Dinks\" for her mother isn\'t just a joke; it\'s a profound point. True help often involves empowering someone to find their own solutions, not doing everything for them.', 'expert-technique', 'timeless', 8, 8, 8, 'You were depressed, and then you did your Shrinky Dinks... You did something about your depression.', 3, 0, '2025-07-22 15:22:53', '2025-07-22 15:22:53', 0, 0),
('DLCOD_IChanneledMyAngerIntoArt_005', 'Permission to Step Back: You\'re Not Unkind for Exiting Family Drama', 'Feeling guilty for pulling back from a complicated family situation? Dr. Laura gives a caller—and all of us—a vital piece of wisdom. When there are other capable adults involved, stepping back isn\'t abandonment; it\'s a healthy act of self-preservation that allows you to lead your own life, separate from the drama. It’s not just okay, it’s necessary.', 'A crucial turning point in the call came when Bridget revealed that her mother, whom she felt obligated to help, wasn\'t living alone. Her brother also lived in the house. This fact completely reframed the situation.\n\nDr. Laura used this to reassure Bridget: \"This is not you changing into an unkind person who doesn\'t want to help others. This is you starting to lead your own life separate from the family drama of your mother and your brother. Perfectly healthy.\"\n\nThis is a critical lesson for parents, who are often conditioned to be caregivers for everyone. You are not abandoning a family member if:\n\n1.  **There are other capable adults who can provide support.** Bridget\'s brother living there means she is not her mother\'s sole source of help.\n2.  **The situation involves ongoing \'drama\' rather than a short-term crisis.** Stepping back from a long-standing, complicated dynamic is different from refusing to help during an emergency.\n3.  **Your involvement is draining resources from your own immediate family.** As Dr. Laura pointed out, Bridget\'s time and energy were finite, and her primary duty was to her husband and children.\n\nBridget confessed, \"It feels odd inside to think that my mother is not my responsibility.\" Dr. Laura\'s response is permission for every parent feeling this way: It\'s not just okay to prioritise your own life and family; it\'s a healthy and necessary part of adulthood.', 'problem-solution', 'timeless', 8, 9, 9, 'This is not you changing into an unkind person... This is you starting to lead your own life separate from the family drama... Perfectly healthy.', 4, 0, '2025-07-22 15:22:53', '2025-07-22 15:22:53', 0, 0),
('drlaura_bad_dad_01', 'You\'re Not Obligated to Care for a Parent Who Wasn\'t There for You', 'Right then. What happens when the parent who was never there for you suddenly turns up, old, ill, and in need of help? It’s a classic guilt-trip waiting to happen. Dr. Laura serves up a rather bracing, but utterly necessary, dose of reality on the subject of obligation. Turns out, you don\'t get a pass for being a rubbish parent just because you got old. Quite the relief, actually.', 'A caller, Christie, shares her story of a father who was largely absent after divorcing her mother when she was 12. He \'basically divorced me too,\' she explains, leaving her to beg for his time. Now, years later, he\'s ill after a fall and has reached out for her help with paperwork and coordinating his care. Christie feels \'a little obligated\' but also \'very resentful.\'\n\nDr. Laura cuts straight to the heart of the matter: \'I don\'t think people get a pass on being crappy to other people just because they\'re old or sick. You\'re not obligated.\'\n\nShe points out the fundamental flaw in the feeling of obligation. Christie\'s father wasn\'t there for her when she was a child who needed him; he didn\'t feel any obligation then. The current situation is not about a sudden rekindling of fatherly love, but a simple matter of need. \'He wants something because he needs something, and you\'re a sucker,\' Dr. Laura says bluntly.\n\nShe reassures Christie that continuing to help is a personal choice, not a requirement. \'Don\'t be resentful about your own personal choice, dear. You don\'t have to make this choice.\' This insight reframes the situation from one of begrudging duty to one of conscious, personal decision-making, which is a far more empowering position to be in.', 'problem-solution', 'timeless', 9, 9, 8, 'I don\'t think people get a pass on being crappy to other people just because they\'re old or sick. You\'re not obligated.', 1, 1, '2025-07-22 17:18:58', '2025-07-22 17:18:58', 0, 0),
('drlaura_bad_dad_02', 'The Simple, Drama-Free Way to Let Go of a Difficult Parent', 'So, you\'ve decided to step back from a draining relationship with a parent. Fantastic. But how do you actually do it without a massive, soul-destroying confrontation? Which, let\'s be honest, nobody has the energy for. Dr. Laura offers a brilliantly simple, low-drama exit strategy.', 'After establishing that Christie is under no obligation to care for her long-absent father, the crucial question arises: how does she actually stop? Christie asks, \'How do I let go and just say, you were never there for me, I\'m not going to be there for you?\'\n\nDr. Laura’s advice is surprisingly gentle and avoids confrontation entirely. \'You don\'t have to say that,\' she advises. \'Just get on with your life. Wish him well.\'\n\nShe explains that initiating a big discussion about past hurts (\'You were not there for me...\') simply opens the door for manipulation (\'Oh, I\'m sorry, but I really need your help now...\'). It creates an opportunity for the other person to make you feel connected again through guilt. The most powerful and healthy approach is to simply and quietly disengage.\n\nDr. Laura concludes with a powerful validation: \'Walk away. Get on with your life. Just like he did. You\'re justified, he wasn\'t.\' This technique isn\'t about seeking revenge or having the last word; it\'s about preserving your own peace and moving forward without getting drawn back into a painful dynamic. It\'s a quiet act of self-preservation.', 'expert-technique', 'timeless', 8, 10, 8, 'You don\'t have to say that. Just get on with your life. Wish him well.', 2, 1, '2025-07-22 17:18:58', '2025-07-22 17:18:58', 0, 0),
('drlaura_bad_dad_03', 'A Father vs. a Sperm Donor: A Crucial Distinction for Dads', 'In a moment of pure, undiluted clarity, Dr. Laura redefines what it means to be a father. It\'s not just about biology, is it? This little exchange is a powerful reminder for all dads about the difference between merely existing and truly showing up for your kids.', 'During a call about her absent father, Christie mentions feeling obligated \'just because he was my father.\' Dr. Laura immediately stops her and makes a crucial distinction.\n\n\'Hold on, just a second,\' she interjects. \'Two things. The word resentment. And... no, he wasn\'t your father. He was the sperm donor. So he wasn\'t your father, that was the point you brought up.\'\n\nThis reframing is a powerful \'aha moment\'. It separates the biological fact of parentage from the active, emotional, and supportive role of being a true father. For Christie, this distinction helps dismantle the misplaced sense of obligation she feels. The man who needs her help now is the one who chose to be just a \'sperm donor\' rather than a \'father\' throughout her childhood.\n\nFor dads, this is a stark reminder of what the job actually entails. Being a father isn\'t a title you get for life just by contributing DNA. It\'s a role you earn every day through presence, involvement, and care. It highlights that the emotional connection and responsibility are what truly define fatherhood in a child\'s eyes.', 'aha-moment', 'timeless', 8, 7, 9, 'No, he wasn\'t your father. He was the sperm donor. So he wasn\'t your father, that was the point you brought up.', 3, 1, '2025-07-22 17:18:58', '2025-07-22 17:18:58', 0, 0),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'The Surprising Reason Bending Custody Rules Might Be the Best Thing for Your Son', 'Feeling a bit guilty for not sticking to the court-ordered custody schedule to the letter? Dr. Laura Schlessinger, in her inimitable style, suggests that sometimes, just sometimes, accommodating your child\'s desire to spend more time with their dad isn\'t a failure, but a rather brilliant act of maternal compassion. Who knew?', 'In a rather touching call, a mother named Sarah confessed her struggle with her son\'s custody arrangement. The court-mandated schedule was clear: her ex-husband picks up their son from school on a Friday and returns him on Monday morning, every two weeks. The problem? Her son and ex-husband frequently ask to extend this time, and feeling guilty about the divorce, Sarah often agrees.\n\nInstead of chastising her, Dr. Laura offered a wonderfully different perspective. She pointed out that it\'s completely natural, especially for a boy, to want to spend more time with his father as he gets older. By accommodating this, Sarah wasn\'t being \'irresponsible\' or failing to adhere to rules; she was being a deeply compassionate and \'exceptional mum\' who was prioritising her son\'s emotional needs and his crucial relationship with his dad.\n\nDr. Laura\'s advice is a powerful reminder that parenting isn\'t always about rigid adherence to schedules. It\'s about adapting to your child\'s evolving needs. The key takeaways were:\n\n1.  **Reframe Your Thinking:** Don\'t see flexibility as a failure. Recognise it as an act of love that supports your son\'s bond with his father.\n2.  **Focus on Your Time:** When your son is with you, make that time special. Do \'mum stuff\', create your own rituals, and be fully present.\n3.  **Encourage, Don\'t Discourage:** Be enthusiastic about the time he spends with his dad. Ask him about it, listen with interest, and never make him feel stressed or guilty. This will actually strengthen your relationship with him, as he\'ll feel relaxed and understood in your presence.\n\nIt\'s a poignant lesson in co-parenting: sometimes, the most loving thing you can do is to graciously support your child\'s relationship with your ex-partner, even if it pulls at your heartstrings a bit.', 'expert-technique', 'timeless', 8, 9, 7, 'I think you\'re being an exceptional mom... You\'re being very compassionate toward your boy.', 1, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'When Your Spiritual Parenting Styles Clash: A Guide for Mismatched Mums and Dads', 'So, you\'re the \'let\'s do a structured Bible study\' type, and your partner is more of a \'let\'s find God in this puddle\' person? Brilliant. It turns out, this isn\'t a recipe for disaster but a rather lovely way to give your kids a well-rounded spiritual education. One of you brings the structure, the other brings the spontaneity. A divine partnership, really.', 'It\'s a common scenario in many households: one parent prefers a structured, scheduled approach to teaching faith (think daily devotionals and prayer times), while the other is more organic, finding spiritual lessons in everyday moments. Rather than seeing this as a conflict, the hosts and guest Danica Cooley suggest reframing it as a complementary strength.\n\nHere’s how it can work wonders:\n\n*   **The Structured Parent:** This parent, like Danica\'s husband, provides consistency and routine. They might lead a family Bible reading after dinner or ensure regular prayer habits. This teaches children that faith is a discipline and a priority that deserves dedicated time.\n*   **The Spontaneous Parent:** This parent, like Danica, excels at \'teaching as you go\'. They might connect a beautiful sunset to God\'s creation or use a moment of conflict to talk about forgiveness. This shows children that faith isn\'t confined to a specific time or place but is woven into the very fabric of daily life.\n\nBy embracing both styles, you\'re not sending mixed messages; you\'re offering a richer, more holistic understanding of faith. The key is to see each other\'s approach as a valuable contribution to the family\'s spiritual life. One brilliant, practical tip offered was the \'Round Robin Reading\' technique for family scripture time. To keep everyone engaged, from the littlest to the most reluctant, each person reads just one paragraph or stanza. This simple method prevents anyone from zoning out and makes it a truly shared experience.', 'practical-tip', 'timeless', 7, 8, 6, 'You\'re trying to guide your kids, you\'re discipling them in the day-to-day, and both of you bring strengths.', 3, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'When Your Parenting Style is \'Aggressively Leaving Things Behind\'', 'If your family holiday feels incomplete without a frantic call back to the hotel about a forgotten pair of swim trunks or a beloved teddy, you\'re not alone. The hosts hilariously confess to being those people who leave a trail of belongings wherever they go. It\'s less a character flaw, more a unique travel strategy, apparently.', 'There are two types of travellers in the world: those who meticulously pack and account for every item, and those who treat every hotel room like a charity donation box. In a moment of pure relatability, host Rich Davis confesses he belongs firmly in the latter category.\n\nDuring a recent trip that took him from Atlanta to New York, he managed to leave something behind at every single stop. It started with his podcasting microphone in a studio in Atlanta. Then, at his co-host Nicole\'s apartment, he left a veritable treasure trove for her kids to discover: swim trunks, shorts, a blanket, toothbrushes, and a pair of tweezers (specifically, a cherished pink Tweezerman, much to his dismay).\n\nThis isn\'t just a one-off; it\'s a lifestyle. It\'s the chaotic reality of travelling with kids and a mind that\'s already juggling a million other things. The story is a funny, reassuring nod to all the dads who are brilliant at many things, but perhaps not at remembering to pack the very items they just unpacked. The conversation playfully normalises this common parenting phenomenon. So next time you realise your kid\'s favourite stuffed animal is currently enjoying an extended stay in another city, take a breath. You\'re not a failure; you\'re just... thorough at leaving your mark on the world.', 'funny-moment', 'timeless', 8, 3, 9, 'I\'m the guy that left one thing everywhere I was... I have figured out how to monetise my friendships.', 4, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('PARENTING_HELL_S10_EP43', 'The Unspoken Agony of the Dodgy Holiday Purchase', 'Every dad has been there. You\'re trying to be a hero, ordering something online to make the kids happy, and what arrives is... well, not quite what was advertised. The hosts\' saga of counterfeit Squishmallows is a hilarious, and painfully relatable, tale of modern consumer woe.', 'Josh Widdicombe recounts a classic parenting tale of online shopping gone wrong. With his children desperate to collect Squishmallows, he and his wife Rose turned to the internet. His daughter bought one with her own pocket money, only for it to arrive shrink-wrapped and looking decidedly unofficial. Then, his son wanted one. They ordered another. It also arrived, a sad, knock-off version with a hole in it.\n\nBut the real comedy came when his wife, Rose, was out and about and, in a moment of weakness, bought two more Squishmallows from a shop, thinking she\'d finally found the genuine article. Alas, she too had been duped, purchasing the \'Ty\' version instead. The result? A house full of unofficial, slightly disappointing squishy toys and a family grappling with the complexities of brand authenticity and online marketplaces.\n\nIt\'s a brilliantly funny and relatable story about the pressures of modern parenting, where keeping up with the latest craze can lead you down a rabbit hole of sponsored links and counterfeit goods. As Rob Beckett points out, the whole situation is made worse by the fact that you can\'t even complain properly because, fundamentally, you know you\'ve bought a knock-off. It’s a perfect snapshot of the small, absurd battles we fight for our kids, often with slightly pathetic results.', 'funny-moment', 'trending', 9, 2, 9, 'She\'s bought the knock-off... She got done in person!', 5, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'The Real Reason Your Teen is Rebelling (It\'s Not What You Think)', 'Ever wonder what\'s really driving your teen\'s rebellious streak? A young man named Andrew, looking back on his own tumultuous years, shares a surprisingly simple truth: it often starts with anger towards parents. It\'s a raw, honest perspective that might just change how you see those slammed doors.', 'In a refreshingly honest conversation, a young man named Andrew opens up about his teenage years, which were marked by sneaking out, drug use, and a constant undercurrent of rebellion. When asked what was driving it all, his answer was disarmingly simple: anger. Specifically, anger towards his parents.\n\nAndrew\'s family moved frequently for his dad\'s job, including a move from the US to Canada. Just as he\'d settle in and make friends, they\'d pack up and leave. He explained, \"Ever since I can remember, I\'ve always had a lot of anger towards my parents... just because we moved around a lot.\" This unresolved anger became the fuel for his rebellion. The sneaking out and drug use weren\'t just about having fun with friends; they were a way to act out against the hurt and instability he felt at home.\n\nThis story is a powerful reminder for dads that teenage rebellion often has deeper roots than just a desire to break rules. It can stem from feelings of powerlessness, unresolved hurt, or a breakdown in connection. The host, Mark Gregston, notes that often, drugs become a component not because teens are inherently bad, but because they\'re looking for a way to cope or escape from their inner turmoil. For Andrew, it wasn\'t about dealing with the anger, but \"forgetting about it and just putting my mind on something else.\"\n\nIt’s a crucial insight: before you can address the behaviour, you have to understand the emotion driving it. That simmering anger might be the real conversation you need to be having with your teen.', 'heartwarming-story', 'timeless', 9, 8, 9, 'A lot of it was just being... rebellious against my parents. Yeah, just because they really hated that.', 2, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'What Reception Teachers *Really* Want Your Child to Know (Hint: It\'s Not Their ABCs)', 'Ever find yourself in a cold sweat, convinced your little one is destined for academic doom because they can\'t yet write a sonnet? A former reception teacher shares what she can\'t easily teach a room of 20 five-year-olds, and it\'s a rather brilliant reality check for us all.', 'When it comes to preparing children for \'big school\', parents often get bogged down in a frenzy of flashcards and phonics. But according to former kindergarten (that\'s Reception to us Brits) teacher Susie Allison, the most crucial skills aren\'t academic at all.\n\nShe explains that while she can teach a child their letters and numbers relatively quickly, there are core life skills that are much harder to instil in a busy classroom environment. These are the skills that truly set a child up for success.\n\nWhat can\'t she teach easily?\n\n1.  **How to Win and Lose Gracefully:** Managing the emotional rollercoaster of games and social interactions is a huge learning curve.\n2.  **How to Ask an Adult for Help:** The confidence to approach a teacher with a question or a problem is fundamental.\n\nThis shifts the focus beautifully. Rather than drilling the alphabet, our energy is better spent on nurturing these foundational social and emotional skills at home. After all, a child who can handle disappointment and ask for help is far better equipped for the classroom than one who simply knows their shapes.', 'expert-technique', 'seasonal', 9, 8, 9, 'It\'s a lot harder in a group of 20 kindergarteners for me to teach your child how to win and lose graciously... than it is for me to teach your child what a shape looks like.', 1, 1, '2025-07-22 15:21:59', '2025-07-22 15:21:59', 0, 0),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'A Real Teacher\'s No-Nonsense \'Big School\' Readiness Checklist', 'Bin the flashcards and step away from the alphabet charts. A former teacher has gifted us a checklist of what *actually* matters for school readiness, and it\'s blessedly free of academic pressure. It\'s all about building a thoughtful, independent little human.', 'If you\'re getting your little one ready for their first year of school, forget the endless academic drills. Former teacher Susie Allison provides a wonderfully practical checklist of skills that form the true foundation for a successful start. The goal isn\'t mastery, but simply developing these abilities.\n\n**The Real Readiness Checklist:**\n\n1.  **Can they follow a multi-step direction?** School mornings are a flurry of instructions: \'Take off your coat, put it in the cubby, pick up a book, and meet me on the rug\'. Practise giving 2-3 step instructions at home and see how many they can manage before, as she puts it, they \'peter off\'.\n\n2.  **Can they speak to an adult?** This is a huge one. Children will encounter many adults at school besides their teacher. Can they confidently ask for help or state a need? A brilliant way to practise is to have them ask for help finding something at the supermarket or ask a question to a staff member at the zoo.\n\n3.  **Do they have basic problem-solving skills?** When a conflict arises with a peer, teachers can\'t always swoop in. Having two or three ideas for solving a problem (e.g., walk away, use the word \'stop\', or suggest a new game) is invaluable. You can role-play these scenarios at home.\n\n4.  **Can they share and take turns?** The school environment is communal. The concept of \'my toy\' largely disappears. Practise phrases like, \"When you\'re done, can I have a turn?\". It’s surprisingly powerful and teaches patience and respect for others.', 'practical-tip', 'seasonal', 10, 10, 9, 'We\'re trying to develop the child as an independent person and a thoughtful thinker. That is the foundation.', 2, 1, '2025-07-22 15:21:59', '2025-07-22 15:21:59', 0, 0),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'The \'Building a House\' Analogy That Will Change How You View School Readiness', 'Are you trying to put the roof on your child\'s \'school house\' before you\'ve even laid the foundation? It\'s a common mistake. This brilliantly simple analogy from a teacher helps reframe our priorities, ensuring we\'re building something sturdy that won\'t come crashing down later.', 'In the rush to prepare our children for school, we often prioritise the \'walls\' of academic skills—like letters, numbers, and writing. However, former teacher Susie Allison offers a much wiser approach using a house-building analogy.\n\nThink of your child\'s education like a house:\n\n*   **The Foundation:** This is comprised of the core readiness skills – social skills, emotional regulation, independence, problem-solving, and communication. These are the things that make a child a \'thoughtful thinker\' and an \'independent person\'.\n*   **The Walls:** These are the academic skills. Reading, writing, maths—all the things we typically associate with \'school\'.\n\nHer point is wonderfully clear: you cannot build sturdy walls on a weak foundation. If we just start throwing up the walls of academics during the toddler and preschool years without first laying a solid foundation of social-emotional skills, the whole structure will eventually \'crack and crumble\'.\n\nThe most important job for parents is to build that strong, solid foundation at home. Once that\'s in place, the teachers can expertly help put up the academic walls. It’s a lovely reminder that our role isn\'t to be a classroom teacher, but to be the architect of a resilient, capable little human.', 'aha-moment', 'timeless', 8, 7, 9, 'If we just start throwing up the walls in the toddler and preschool years without a solid foundation, it will eventually crack and crumble.', 3, 1, '2025-07-22 15:21:59', '2025-07-22 15:21:59', 0, 0),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'Ditch the Flashcards: The Real Mission is Raising an Independent Human', 'It\'s easy to get lost in the sea of educational toys and apps, thinking we\'re preparing our children for life. But what if the real goal isn\'t just knowing things, but being able to navigate the world independently? A rather liberating thought, isn\'t it?', 'The conversation around school readiness often gets hijacked by a narrow focus on \'flashcard skills\' – the ability to rapidly recall letters, numbers, and colours. While these are useful, former teacher Susie Allison argues that this misses the entire point.\n\nThe real mission, she suggests, is to shift our perspective. We should be asking ourselves: \"How can I make sure my child\'s first serious step into an independent situation is a successful one?\"\n\nThis isn\'t just about the first day of school; it\'s about the beginning of them creating a world separate from us. At school, they will start to build their own life, with their own jokes, friends, and challenges. Our job is to equip them with the tools to manage that new world.\n\nInstead of worrying if they can recall the sound the letter \'C\' makes, we should be ensuring they feel confident enough to approach an adult and say, \'I need help\'. These are vastly different skills, and the latter is far more crucial for their well-being and long-term success. The confidence and independence we foster at home are what will truly see them through their school journey and beyond.', 'research-insight', 'timeless', 8, 8, 10, 'This really is their first step into this very independent world where they\'re going to create a world very separate from you.', 4, 0, '2025-07-22 15:21:59', '2025-07-22 15:21:59', 0, 0),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'The \'Brothers\' Hug\': A Genius Hack for Ending Sibling Squabbles', 'Sibling fights. They\'re the soundtrack to many a family home. But author Justin Whitmel Earley has a rather brilliant, and physically simple, technique for turning conflict into connection: a mandatory hug that lasts until both kids are smiling. It sounds mad, but honestly, it\'s genius.', 'When your kids are at each other\'s throats, the last thing you\'d think to do is force them into a hug. Yet, that\'s exactly the advice from Justin Whitmel Earley, author and dad of four boys. He calls it the \'Brothers\' Hug\', a simple but profound habit of reconciliation.\n\nHere\'s how it works:\n\n1.  **Acknowledge and Apologise:** After a fight, you steward the children towards apologising and forgiving each other. The words matter, as they lay the groundwork for what\'s next.\n2.  **The Reconciliation Hug:** Once words have been exchanged, you institute the hug. The rule is simple: \"You\'ve got to hold on until both of you start smiling again.\"\n3.  **From Awkward to Authentic:** What starts as an awkward, forced embrace almost inevitably dissolves into genuine laughter. It\'s a form of semi-wrestling that breaks the tension. As Earley puts it, \"it moves their mind by moving their body.\"\n\nThe magic of this technique is that it\'s an embodied act of reconciliation. It\'s hard to stay furious with someone you\'re physically connected to, especially when the goal is to make each other laugh. It transforms a moment of conflict from a purely verbal (and often insincere) apology into a physical and emotional reconnection.\n\nEarley notes that this isn\'t just for little ones. The principle applies to all relationships, including with our partners. After an argument, a simple act like holding hands, taking a walk, or giving a proper hug can bridge the emotional gap far quicker than words alone. It\'s about physically moving towards reconciliation, a powerful lesson for the whole family.', 'expert-technique', 'timeless', 9, 10, 9, 'It moves their mind by moving their body. And they started to play halfway through the hug, they started to smile. And that became a tradition in our family.', 1, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'Ziggy Marley\'s Secret to Calmer Bedtimes? Turn It Into a Jam Session.', 'Struggling with bedtime battles? Take a leaf out of Ziggy Marley\'s book. The Grammy-winning artist and dad of seven reveals his rather brilliant method for getting his kids ready for bed: he turns mundane routines into catchy jingles. It\'s a simple, joyful way to transform a nightly chore into a moment of connection.', 'For many dads, the bedtime routine can feel like a nightly negotiation with a tiny, sleep-averse dictator. But what if you could change the entire mood with a simple trick? In a delightful interview, musician Ziggy Marley shares his secret weapon for a smoother evening with his children.\n\nInstead of just telling them it\'s time to brush their teeth or get into their pyjamas, he makes up a song about it. He explains, \"I\'d kind of sing a song... \'Brush your teeth, brush your teeth\'. I make up something, I make a jingle.\" He even created a jingle around the word \'pajamin\', a playful riff on his father Bob Marley\'s classic song \'Jammin\'\', which became the inspiration for his children\'s book.\n\nThis technique is genius for a few reasons:\n\n*   **It\'s Playful:** Music and silliness immediately defuse the tension that can build around bedtime.\n*   **It\'s Connecting:** Singing together is a shared activity that builds warmth and connection, rather than creating a power struggle.\n*   **It\'s Creative:** It transforms a mundane task into a moment of fun and imagination for both parent and child.\n\nThis isn\'t about being a professional musician; it\'s about being willing to be a bit silly. You don\'t need a perfect melody or clever lyrics. Just a simple, repetitive chant like \'Time for bed, sleepy head!\' can work wonders. It\'s a fantastic reminder that sometimes the best parenting tools are the ones that bring a little more play and a lot less pressure into our daily routines.', 'practical-tip', 'timeless', 9, 10, 10, 'It\'s just another positive energy and... when you\'re petting the dog, sometimes when I\'m petting the dog for the dog, I\'m petting the dog for us.', 1, 1, '2025-07-22 15:48:49', '2025-07-22 15:48:49', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `article_age_groups`
--

CREATE TABLE `article_age_groups` (
  `article_id` varchar(255) NOT NULL,
  `age_group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_age_groups`
--

INSERT INTO `article_age_groups` (`article_id`, `age_group_id`) VALUES
('CARE_FEEDING_ONLY_CHILD', 4),
('CARE_FEEDING_ONLY_CHILD', 5),
('DLCOD_IChanneledMyAngerIntoArt_001', 9),
('DLCOD_IChanneledMyAngerIntoArt_002', 9),
('DLCOD_IChanneledMyAngerIntoArt_003', 9),
('DLCOD_IChanneledMyAngerIntoArt_004', 9),
('DLCOD_IChanneledMyAngerIntoArt_005', 9),
('drlaura_bad_dad_01', 8),
('drlaura_bad_dad_01', 9),
('drlaura_bad_dad_02', 8),
('drlaura_bad_dad_02', 9),
('drlaura_bad_dad_03', 9),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 5),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 6),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 5),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 6),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 7),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 9),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 9),
('PARENTING_HELL_S10_EP43', 4),
('PARENTING_HELL_S10_EP43', 5),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 7),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 3),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 5),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 4),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 5),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 6),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 3),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 4),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 5);

-- --------------------------------------------------------

--
-- Table structure for table `article_categories`
--

CREATE TABLE `article_categories` (
  `article_id` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_categories`
--

INSERT INTO `article_categories` (`article_id`, `category_id`) VALUES
('CARE_FEEDING_ONLY_CHILD', 3),
('CARE_FEEDING_ONLY_CHILD', 7),
('CARE_FEEDING_ONLY_CHILD', 9),
('DLCOD_IChanneledMyAngerIntoArt_001', 3),
('DLCOD_IChanneledMyAngerIntoArt_001', 6),
('DLCOD_IChanneledMyAngerIntoArt_001', 7),
('DLCOD_IChanneledMyAngerIntoArt_001', 15),
('DLCOD_IChanneledMyAngerIntoArt_002', 3),
('DLCOD_IChanneledMyAngerIntoArt_002', 7),
('DLCOD_IChanneledMyAngerIntoArt_002', 15),
('DLCOD_IChanneledMyAngerIntoArt_002', 16),
('DLCOD_IChanneledMyAngerIntoArt_003', 2),
('DLCOD_IChanneledMyAngerIntoArt_003', 3),
('DLCOD_IChanneledMyAngerIntoArt_003', 7),
('DLCOD_IChanneledMyAngerIntoArt_003', 15),
('DLCOD_IChanneledMyAngerIntoArt_004', 3),
('DLCOD_IChanneledMyAngerIntoArt_004', 7),
('DLCOD_IChanneledMyAngerIntoArt_004', 15),
('DLCOD_IChanneledMyAngerIntoArt_005', 3),
('DLCOD_IChanneledMyAngerIntoArt_005', 7),
('DLCOD_IChanneledMyAngerIntoArt_005', 15),
('drlaura_bad_dad_01', 3),
('drlaura_bad_dad_01', 7),
('drlaura_bad_dad_01', 15),
('drlaura_bad_dad_02', 4),
('drlaura_bad_dad_02', 7),
('drlaura_bad_dad_02', 15),
('drlaura_bad_dad_03', 7),
('drlaura_bad_dad_03', 16),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 3),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 7),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 16),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 4),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 7),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 16),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 7),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 15),
('PARENTING_HELL_S10_EP43', 10),
('PARENTING_HELL_S10_EP43', 15),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 2),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 3),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 4),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 7),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 3),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 5),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 9),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 2),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 5),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 9),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 3),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 5),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 7),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 3),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 7),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 9),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 15),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 2),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 3),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 7),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 1),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 2),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 10);

-- --------------------------------------------------------

--
-- Table structure for table `article_tags`
--

CREATE TABLE `article_tags` (
  `article_id` varchar(255) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `article_tags`
--

INSERT INTO `article_tags` (`article_id`, `tag_id`) VALUES
('CARE_FEEDING_ONLY_CHILD', 52),
('CARE_FEEDING_ONLY_CHILD', 81),
('CARE_FEEDING_ONLY_CHILD', 82),
('CARE_FEEDING_ONLY_CHILD', 83),
('CARE_FEEDING_ONLY_CHILD', 84),
('DLCOD_IChanneledMyAngerIntoArt_001', 5),
('DLCOD_IChanneledMyAngerIntoArt_001', 23),
('DLCOD_IChanneledMyAngerIntoArt_001', 24),
('DLCOD_IChanneledMyAngerIntoArt_001', 25),
('DLCOD_IChanneledMyAngerIntoArt_001', 26),
('DLCOD_IChanneledMyAngerIntoArt_001', 27),
('DLCOD_IChanneledMyAngerIntoArt_001', 28),
('DLCOD_IChanneledMyAngerIntoArt_001', 29),
('DLCOD_IChanneledMyAngerIntoArt_002', 30),
('DLCOD_IChanneledMyAngerIntoArt_002', 31),
('DLCOD_IChanneledMyAngerIntoArt_002', 32),
('DLCOD_IChanneledMyAngerIntoArt_002', 33),
('DLCOD_IChanneledMyAngerIntoArt_002', 34),
('DLCOD_IChanneledMyAngerIntoArt_002', 35),
('DLCOD_IChanneledMyAngerIntoArt_002', 36),
('DLCOD_IChanneledMyAngerIntoArt_002', 37),
('DLCOD_IChanneledMyAngerIntoArt_002', 38),
('DLCOD_IChanneledMyAngerIntoArt_003', 5),
('DLCOD_IChanneledMyAngerIntoArt_003', 24),
('DLCOD_IChanneledMyAngerIntoArt_003', 29),
('DLCOD_IChanneledMyAngerIntoArt_003', 30),
('DLCOD_IChanneledMyAngerIntoArt_003', 31),
('DLCOD_IChanneledMyAngerIntoArt_003', 39),
('DLCOD_IChanneledMyAngerIntoArt_003', 40),
('DLCOD_IChanneledMyAngerIntoArt_003', 41),
('DLCOD_IChanneledMyAngerIntoArt_004', 30),
('DLCOD_IChanneledMyAngerIntoArt_004', 31),
('DLCOD_IChanneledMyAngerIntoArt_004', 41),
('DLCOD_IChanneledMyAngerIntoArt_004', 42),
('DLCOD_IChanneledMyAngerIntoArt_004', 43),
('DLCOD_IChanneledMyAngerIntoArt_004', 44),
('DLCOD_IChanneledMyAngerIntoArt_004', 45),
('DLCOD_IChanneledMyAngerIntoArt_004', 46),
('DLCOD_IChanneledMyAngerIntoArt_005', 30),
('DLCOD_IChanneledMyAngerIntoArt_005', 31),
('DLCOD_IChanneledMyAngerIntoArt_005', 47),
('DLCOD_IChanneledMyAngerIntoArt_005', 48),
('DLCOD_IChanneledMyAngerIntoArt_005', 49),
('DLCOD_IChanneledMyAngerIntoArt_005', 50),
('DLCOD_IChanneledMyAngerIntoArt_005', 51),
('DLCOD_IChanneledMyAngerIntoArt_005', 52),
('drlaura_bad_dad_01', 30),
('drlaura_bad_dad_01', 31),
('drlaura_bad_dad_01', 97),
('drlaura_bad_dad_01', 98),
('drlaura_bad_dad_01', 99),
('drlaura_bad_dad_01', 100),
('drlaura_bad_dad_01', 101),
('drlaura_bad_dad_02', 30),
('drlaura_bad_dad_02', 50),
('drlaura_bad_dad_02', 69),
('drlaura_bad_dad_02', 102),
('drlaura_bad_dad_02', 103),
('drlaura_bad_dad_02', 104),
('drlaura_bad_dad_03', 105),
('drlaura_bad_dad_03', 106),
('drlaura_bad_dad_03', 107),
('drlaura_bad_dad_03', 108),
('drlaura_bad_dad_03', 109),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 53),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 54),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 55),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 56),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 57),
('DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 58),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 54),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 59),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 60),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 61),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 62),
('FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 63),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 76),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 77),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 78),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 79),
('HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 80),
('PARENTING_HELL_S10_EP43', 85),
('PARENTING_HELL_S10_EP43', 86),
('PARENTING_HELL_S10_EP43', 87),
('PARENTING_HELL_S10_EP43', 88),
('PARENTING_HELL_S10_EP43', 89),
('PARENTING_HELL_S10_EP43', 90),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 64),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 65),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 66),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 67),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 68),
('PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 69),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 1),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 2),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 3),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 4),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 5),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 6),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 7),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 8),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 9),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 10),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 11),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 12),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 13),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 1),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 14),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 15),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 16),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 17),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 18),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 11),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 16),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 19),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 20),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 21),
('pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 22),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 70),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 71),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 72),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 73),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 74),
('RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 75),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 91),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 92),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 93),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 94),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 95),
('RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 96);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `display_name`, `description`, `sort_order`) VALUES
(1, 'sleep-solutions', 'Sleep Solutions', 'Tips and strategies for better sleep', 1),
(2, 'behaviour-management', 'Behaviour Management', 'Managing challenging behaviours', 2),
(3, 'emotional-intelligence', 'Emotional Intelligence', 'Building emotional skills', 3),
(4, 'communication-skills', 'Communication Skills', 'Improving parent-child communication', 4),
(5, 'educational-support', 'Educational Support', 'Supporting learning and development', 5),
(6, 'health-wellness', 'Health & Wellness', 'Physical and mental health advice', 6),
(7, 'family-dynamics', 'Family Dynamics', 'Building stronger family relationships', 7),
(8, 'screen-time-tech', 'Screen Time & Tech', 'Managing technology and screen time', 8),
(9, 'social-development', 'Social Development', 'Building social skills and friendships', 9),
(10, 'creativity-play', 'Creativity & Play', 'Encouraging creative expression', 10),
(11, 'nutrition-feeding', 'Nutrition & Feeding', 'Healthy eating and feeding advice', 11),
(12, 'safety-protection', 'Safety & Protection', 'Keeping children safe', 12),
(13, 'special-needs', 'Special Needs', 'Supporting children with special needs', 13),
(14, 'working-parent-tips', 'Working Parent Tips', 'Balancing work and parenting', 14),
(15, 'self-care-parent', 'Parent Self-Care', 'Taking care of yourself as a parent', 15),
(16, 'relationship-partnership', 'Relationship & Partnership', 'Maintaining relationships while parenting', 16);

-- --------------------------------------------------------

--
-- Table structure for table `migration_log`
--

CREATE TABLE `migration_log` (
  `id` int(11) NOT NULL,
  `migration_name` varchar(255) NOT NULL,
  `executed_at` timestamp NULL DEFAULT current_timestamp(),
  `success` tinyint(1) DEFAULT 1,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration_log`
--

INSERT INTO `migration_log` (`id`, `migration_name`, `executed_at`, `success`, `notes`) VALUES
(1, '001_user_management_schema', '2025-07-22 17:35:51', 1, 'Added users table, user_bookmarks table, and extended articles table with engagement tracking');

-- --------------------------------------------------------

--
-- Table structure for table `related_topics`
--

CREATE TABLE `related_topics` (
  `id` int(11) NOT NULL,
  `article_id` varchar(255) DEFAULT NULL,
  `topic` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `related_topics`
--

INSERT INTO `related_topics` (`id`, `article_id`, `topic`) VALUES
(1, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'starting school'),
(2, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'parenting anxiety'),
(3, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'life skills for kids'),
(4, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'building confidence'),
(5, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'school preparation'),
(6, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'practical parenting'),
(7, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'child development'),
(8, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'emotional intelligence'),
(9, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'holistic development'),
(10, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'early childhood education'),
(11, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'parenting mindset'),
(12, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'long-term thinking'),
(13, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'raising capable kids'),
(14, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'letting go'),
(15, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'parenting philosophy'),
(16, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'child psychology'),
(17, 'DLCOD_IChanneledMyAngerIntoArt_001', 'mental health for parents'),
(18, 'DLCOD_IChanneledMyAngerIntoArt_001', 'finding time for hobbies'),
(19, 'DLCOD_IChanneledMyAngerIntoArt_001', 'improving family connection'),
(20, 'DLCOD_IChanneledMyAngerIntoArt_001', 'breaking negative cycles'),
(21, 'DLCOD_IChanneledMyAngerIntoArt_002', 'managing parental guilt'),
(22, 'DLCOD_IChanneledMyAngerIntoArt_002', 'setting boundaries with parents'),
(23, 'DLCOD_IChanneledMyAngerIntoArt_002', 'balancing family needs'),
(24, 'DLCOD_IChanneledMyAngerIntoArt_002', 'avoiding burnout'),
(25, 'DLCOD_IChanneledMyAngerIntoArt_003', 'managing difficult emotions'),
(26, 'DLCOD_IChanneledMyAngerIntoArt_003', 'how to stop feeling guilty'),
(27, 'DLCOD_IChanneledMyAngerIntoArt_003', 'maintaining boundaries'),
(28, 'DLCOD_IChanneledMyAngerIntoArt_003', 'cognitive behavioural techniques'),
(29, 'DLCOD_IChanneledMyAngerIntoArt_004', 'dealing with depressed family members'),
(30, 'DLCOD_IChanneledMyAngerIntoArt_004', 'how to support without enabling'),
(31, 'DLCOD_IChanneledMyAngerIntoArt_004', 'setting healthy family boundaries'),
(32, 'DLCOD_IChanneledMyAngerIntoArt_004', 'parenting adult parents'),
(33, 'DLCOD_IChanneledMyAngerIntoArt_005', 'handling difficult family dynamics'),
(34, 'DLCOD_IChanneledMyAngerIntoArt_005', 'managing guilt with siblings and parents'),
(35, 'DLCOD_IChanneledMyAngerIntoArt_005', 'emotional boundaries'),
(36, 'DLCOD_IChanneledMyAngerIntoArt_005', 'prioritising your mental health'),
(37, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'navigating divorce with children'),
(38, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'blended families'),
(39, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'single parenting'),
(40, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'teaching values'),
(41, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'family traditions'),
(42, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'marriage and parenting'),
(43, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'understanding teenagers'),
(44, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'discipline'),
(45, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'mental health in teens'),
(46, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'teaching empathy'),
(47, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'positive discipline'),
(48, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'family connection'),
(49, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'parenting fails'),
(50, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'dad humour'),
(51, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'organising for family trips'),
(52, 'CARE_FEEDING_ONLY_CHILD', 'single-child families'),
(53, 'CARE_FEEDING_ONLY_CHILD', 'making friends for kids'),
(54, 'CARE_FEEDING_ONLY_CHILD', 'childhood loneliness'),
(55, 'PARENTING_HELL_S10_EP43', 'toy crazes'),
(56, 'PARENTING_HELL_S10_EP43', 'managing expectations'),
(57, 'PARENTING_HELL_S10_EP43', 'consumerism'),
(58, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'making routines fun'),
(59, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'toddler behaviour'),
(60, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'positive parenting'),
(61, 'drlaura_bad_dad_01', 'setting boundaries'),
(62, 'drlaura_bad_dad_01', 'parental guilt'),
(63, 'drlaura_bad_dad_01', 'family conflict'),
(64, 'drlaura_bad_dad_01', 'emotional labour'),
(65, 'drlaura_bad_dad_02', 'avoiding confrontation'),
(66, 'drlaura_bad_dad_02', 'emotional self-care'),
(67, 'drlaura_bad_dad_02', 'healing from neglect'),
(68, 'drlaura_bad_dad_02', 'generational trauma'),
(69, 'drlaura_bad_dad_03', 'defining fatherhood'),
(70, 'drlaura_bad_dad_03', 'being present'),
(71, 'drlaura_bad_dad_03', 'dads\' roles'),
(72, 'drlaura_bad_dad_03', 'emotional availability');

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` int(11) NOT NULL,
  `article_id` varchar(255) DEFAULT NULL,
  `podcast_title` varchar(500) DEFAULT NULL,
  `episode_title` varchar(500) DEFAULT NULL,
  `episode_url` varchar(1000) DEFAULT NULL,
  `media_url` varchar(1000) DEFAULT NULL,
  `timestamp` varchar(20) DEFAULT NULL,
  `host_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `article_id`, `podcast_title`, `episode_title`, `episode_url`, `media_url`, `timestamp`, `host_name`) VALUES
(1, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'The PedsDocTalk Podcast: Child Health, Development & Parenting—From a Pediatrician Mom', 'The Follow-Up: Kindergarten Readiness Is Not What You Think', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', '02:05', 'Dr. Mona Amin'),
(2, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'The PedsDocTalk Podcast: Child Health, Development & Parenting—From a Pediatrician Mom', 'The Follow-Up: Kindergarten Readiness Is Not What You Think', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', '04:42', 'Dr. Mona Amin'),
(3, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'The PedsDocTalk Podcast: Child Health, Development & Parenting—From a Pediatrician Mom', 'The Follow-Up: Kindergarten Readiness Is Not What You Think', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', '04:08', 'Dr. Mona Amin'),
(4, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'The PedsDocTalk Podcast: Child Health, Development & Parenting—From a Pediatrician Mom', 'The Follow-Up: Kindergarten Readiness Is Not What You Think', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', 'https://pdst.fm/e/tracking.swap.fm/track/JhoQDAATtO1l0y8tdKNa/traffic.megaphone.fm/ADL3865311828.mp3?updated=1752606464', '02:30', 'Dr. Mona Amin'),
(5, 'DLCOD_IChanneledMyAngerIntoArt_001', 'Dr. Laura Call of the Day', 'I Channeled My Anger into Art', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b&feed=ZTNLWlKG', '00:00:22', 'Dr. Laura Schlessinger'),
(6, 'DLCOD_IChanneledMyAngerIntoArt_002', 'Dr. Laura Call of the Day', 'I Channeled My Anger into Art', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b&feed=ZTNLWlKG', '02:49', 'Dr. Laura Schlessinger'),
(7, 'DLCOD_IChanneledMyAngerIntoArt_003', 'Dr. Laura Call of the Day', 'I Channeled My Anger into Art', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b&feed=ZTNLWlKG', '07:24', 'Dr. Laura Schlessinger'),
(8, 'DLCOD_IChanneledMyAngerIntoArt_004', 'Dr. Laura Call of the Day', 'I Channeled My Anger into Art', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b&feed=ZTNLWlKG', '04:19', 'Dr. Laura Schlessinger'),
(9, 'DLCOD_IChanneledMyAngerIntoArt_005', 'Dr. Laura Call of the Day', 'I Channeled My Anger into Art', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=52446e7b-3a46-4ff7-8df5-1bcdb6df9f7b&feed=ZTNLWlKG', '06:07', 'Dr. Laura Schlessinger'),
(10, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'Dr. Laura Call of the Day', 'Refresher: How Can I Help My Son Feel More Stability In A Broken Home?', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/86317c9c-c257-48d5-a6cc-ae302b114c8d/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=86317c9c-c257-48d5-a6cc-ae302b114c8d&feed=ZTNLWlKG', '00:00:16', 'Dr. Laura Schlessinger'),
(11, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'Focus on Parenting Podcast', 'How You Both Teach Your Kids About God', 'https://omny.fm/shows/focus-on-parenting-podcast/how-you-both-teach-your-kids-about-god', 'https://traffic.omny.fm/d/clips/6b62b447-e557-44a9-ae88-af6300da5440/7cb9a9ac-1278-4e09-8bfc-af88014d8ebd/fa954a7c-7bf4-4e5b-9e9c-b3220083b778/audio.mp3?utm_source=Podcast&in_playlist=ea400406-69c1-404e-b75b-af88014d8ecb', '00:09:34', 'John Fuller, Dr. Danny Huerta'),
(12, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'Parenting Today\'s Teens', 'Teen Stories: What Really Drives Rebellion', 'https://parentingtodaysteens.org', 'https://dts.podtrac.com/redirect.mp3/pscrb.fm/rss/p/audio2.redcircle.com/episodes/f51dd049-5c58-40a9-bc36-ed51075147cc/stream.mp3', '00:03:15', 'Mark Gregston'),
(13, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'Raising Boys & Girls', 'Episode 291: Living in the Mess and Helping Siblings Get Along with Justin Whitmel Earley', 'https://pdst.fm/e/pscrb.fm/rss/p/chrt.fm/track/DC65F1/pfx.vpixl.com/7i87e/mgln.ai/e/424/traffic.megaphone.fm/TSF8718474968.mp3?updated=1752698873', NULL, '00:21:55', 'Sissy Goff, David Thomas'),
(14, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'Have Kids, They Said…', 'Soulmates or is the drugs?', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/958f8531-11a9-4a29-9b20-6b3546197eb5/episodes/8e91504d-68ea-4cf4-a181-614608cea587/audio/128/default.mp3?aid=rss_feed&awCollectionId=958f8531-11a9-4a29-9b20-6b3546197eb5&awEpisodeId=8e91504d-68ea-4cf4-a181-614608cea587&feed=FJo1cu4f', '00:20:05', 'Rich Davis, Nicole Ryan'),
(15, 'CARE_FEEDING_ONLY_CHILD', 'Care and Feeding | Slate\'s parenting show', 'My Only Child Wants a Sibling. What Now?', 'https://www.podtrac.com/pts/redirect.mp3/pdst.fm/e/verifi.podscribe.com/rss/p/chtbl.com/track/28D492/go.slate.me/p/tracking.swap.fm/track/eyyXAri85YMcZmCU4WwO/traffic.megaphone.fm/SLT1798673415.mp3?updated=1753046335', NULL, '00:19:40', 'Elizabeth Newcamp, Lucy Lopez'),
(16, 'PARENTING_HELL_S10_EP43', 'Parenting Hell with Rob Beckett and Josh Widdicombe', 'S10 EP43: Instagram vs. Reality', '', 'https://traffic.megaphone.fm/GLT9233361095.mp3?updated=1753141651', '00:09:20', 'Rob Beckett, Josh Widdicombe'),
(17, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'Raising Boys & Girls', 'Episode 292: Curing the Bedtime Blues with Ziggy Marley', 'https://pdst.fm/e/pscrb.fm/rss/p/chrt.fm/track/DC65F1/pfx.vpixl.com/7i87e/mgln.ai/e/424/traffic.megaphone.fm/TSF5663660505.mp3?updated=1753129902', NULL, '00:13:15', 'Sissy Goff, David Thomas'),
(18, 'drlaura_bad_dad_01', 'Dr. Laura Call of the Day', 'I Have a Bad Dad', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/7757c6a3-36b8-41e1-9d29-be87573c08c7/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=7757c6a3-36b8-41e1-9d29-be87573c08c7&feed=ZTNLWlKG', '00:01:27', 'Dr. Laura Schlessinger'),
(19, 'drlaura_bad_dad_02', 'Dr. Laura Call of the Day', 'I Have a Bad Dad', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/7757c6a3-36b8-41e1-9d29-be87573c08c7/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=7757c6a3-36b8-41e1-9d29-be87573c08c7&feed=ZTNLWlKG', '00:03:56', 'Dr. Laura Schlessinger'),
(20, 'drlaura_bad_dad_03', 'Dr. Laura Call of the Day', 'I Have a Bad Dad', 'https://www.siriusxm.com', 'https://dts.podtrac.com/redirect.mp3/tracking.swap.fm/track/0bDcdoop59bdTYSfajQW/sxm.simplecastaudio.com/01fcb57e-c0e3-4101-8464-5f6e0a1d710e/episodes/7757c6a3-36b8-41e1-9d29-be87573c08c7/audio/128/default.mp3?aid=rss_feed&awCollectionId=01fcb57e-c0e3-4101-8464-5f6e0a1d710e&awEpisodeId=7757c6a3-36b8-41e1-9d29-be87573c08c7&feed=ZTNLWlKG', '00:01:27', 'Dr. Laura Schlessinger');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`) VALUES
(106, 'absent father'),
(99, 'ageing parents'),
(14, 'analogy'),
(65, 'anger'),
(73, 'apologies'),
(28, 'art therapy'),
(91, 'bedtime routine'),
(62, 'bible study'),
(30, 'boundaries'),
(83, 'building community'),
(10, 'checklist'),
(16, 'child development'),
(27, 'childhood trauma'),
(51, 'co-dependency'),
(54, 'co-parenting'),
(86, 'collecting'),
(20, 'confidence'),
(72, 'conflict resolution'),
(39, 'coping mechanism'),
(25, 'creative outlet'),
(53, 'custody'),
(44, 'depression'),
(102, 'disengagement'),
(55, 'divorce'),
(67, 'drug use'),
(109, 'emotional connection'),
(17, 'emotional foundation'),
(5, 'emotional regulation'),
(42, 'enabling'),
(35, 'energy management'),
(97, 'estranged parent'),
(32, 'extended family'),
(59, 'faith'),
(69, 'family conflict'),
(47, 'family drama'),
(34, 'family obligations'),
(45, 'family responsibility'),
(80, 'family travel'),
(63, 'family worship'),
(56, 'father-son bond'),
(105, 'fatherhood'),
(71, 'fighting'),
(58, 'flexible parenting'),
(78, 'forgetfulness'),
(74, 'forgiveness'),
(82, 'friendships'),
(31, 'guilt'),
(43, 'helping vs enabling'),
(26, 'hobby'),
(90, 'humour'),
(11, 'independence'),
(2, 'kindergarten'),
(7, 'kindergarten readiness'),
(50, 'letting go'),
(9, 'life skills'),
(18, 'long-term success'),
(41, 'mental health'),
(37, 'mother-daughter'),
(94, 'music'),
(104, 'no contact'),
(98, 'obligation'),
(88, 'online shopping'),
(81, 'only child'),
(68, 'parent-teen communication'),
(23, 'parental anger'),
(57, 'parental guilt'),
(108, 'parental responsibility'),
(84, 'parenting advice'),
(89, 'parenting fails'),
(19, 'parenting goals'),
(75, 'parenting hacks'),
(76, 'parenting humour'),
(15, 'parenting philosophy'),
(61, 'parenting styles'),
(93, 'parenting tips'),
(95, 'playful parenting'),
(29, 'positive habits'),
(33, 'priorities'),
(12, 'problem-solving'),
(3, 'reception year'),
(40, 'redirecting thoughts'),
(79, 'relatable parenting'),
(101, 'resentment'),
(21, 'resilience'),
(48, 'responsibility'),
(38, 'sandwich generation'),
(36, 'saying no'),
(1, 'school readiness'),
(22, 'school transition'),
(24, 'self-care'),
(49, 'self-preservation'),
(13, 'sharing'),
(70, 'sibling rivalry'),
(52, 'siblings'),
(4, 'social skills'),
(60, 'spirituality'),
(87, 'Squishmallows'),
(8, 'starting school'),
(46, 'taking action'),
(6, 'teacher perspective'),
(66, 'teen behaviour'),
(64, 'teenage rebellion'),
(92, 'toddler bedtime'),
(100, 'toxic family'),
(103, 'toxic parents'),
(85, 'toys'),
(77, 'travel with kids'),
(107, 'what makes a dad'),
(96, 'Ziggy Marley');

-- --------------------------------------------------------

--
-- Table structure for table `takeaways`
--

CREATE TABLE `takeaways` (
  `id` int(11) NOT NULL,
  `article_id` varchar(255) DEFAULT NULL,
  `takeaway` text NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `takeaways`
--

INSERT INTO `takeaways` (`id`, `article_id`, `takeaway`, `sort_order`) VALUES
(1, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'Shift focus from academic drills to practising social skills through play.', 1),
(2, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'Talk through scenarios of winning and losing in games at home.', 2),
(3, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'Practise asking for help in low-stakes situations, like at the supermarket.', 3),
(4, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_1', 'Remind yourself that teachers are experts at teaching academics; your expertise is in teaching life\'s softer skills.', 4),
(5, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'Practise giving your child two or three simple instructions in a row at home.', 1),
(6, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'Encourage your child to be the one to ask for directions or help from staff when you\'re out shopping or at an attraction.', 2),
(7, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'Talk through \'what if\' scenarios for common playground squabbles.', 3),
(8, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_2', 'Teach the phrase \'When you\'re done, can I have a turn?\' as a go-to for sharing situations.', 4),
(9, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'Before the school year, assess your child\'s \'foundation\' - their ability to communicate, share, and solve small problems.', 1),
(10, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'View academic learning as the \'walls\' that can only be built once the social-emotional \'foundation\' is secure.', 2),
(11, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_3', 'Trust that teachers are skilled at building the academic \'walls\' and focus your energy on the foundational work they can\'t do in a large group.', 3),
(12, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'Frame school readiness not as an academic test, but as a launchpad for independence.', 1),
(13, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'Prioritise skills that help your child navigate social situations without you.', 2),
(14, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'When feeling anxious about academics, remind yourself of the bigger picture: raising a capable, independent person.', 3),
(15, 'pedsdoctalk_kindergarten_readiness_is_not_what_you_think_4', 'Celebrate small acts of independence at home to build their confidence for school.', 4),
(16, 'DLCOD_IChanneledMyAngerIntoArt_001', 'Identify one small, simple creative activity you enjoyed as a child (colouring, plasticine, etc.).', 1),
(17, 'DLCOD_IChanneledMyAngerIntoArt_001', 'Dedicate just 15-20 minutes a few times a week to this activity.', 2),
(18, 'DLCOD_IChanneledMyAngerIntoArt_001', 'Notice how taking time for a simple, joyful craft can positively impact other areas of your life.', 3),
(19, 'DLCOD_IChanneledMyAngerIntoArt_001', 'Use creative outlets as a healthy way to process difficult emotions like anger or frustration.', 4),
(20, 'DLCOD_IChanneledMyAngerIntoArt_002', 'Audit your time: write down where your non-work, non-childcare hours are going.', 1),
(21, 'DLCOD_IChanneledMyAngerIntoArt_002', 'Recognise that reallocating time from extended family to your immediate family is not selfish, but a healthy priority shift.', 2),
(22, 'DLCOD_IChanneledMyAngerIntoArt_002', 'When feeling guilty, remind yourself of the positive impact your presence has on your own children and partner.', 3),
(23, 'DLCOD_IChanneledMyAngerIntoArt_002', 'Communicate your new boundaries clearly but kindly, without over-explaining or apologising.', 4),
(24, 'DLCOD_IChanneledMyAngerIntoArt_003', 'Identify your personal \'Shrinky Dink\' - a simple, positive activity that brings you peace or joy.', 1),
(25, 'DLCOD_IChanneledMyAngerIntoArt_003', 'Keep the tools for this activity easily accessible.', 2),
(26, 'DLCOD_IChanneledMyAngerIntoArt_003', 'The moment you feel a wave of guilt or sadness about a boundary, immediately engage in that activity, even for just a few minutes.', 3),
(27, 'DLCOD_IChanneledMyAngerIntoArt_003', 'Acknowledge the feeling, then deliberately shift your physical and mental energy to the positive action.', 4),
(28, 'DLCOD_IChanneledMyAngerIntoArt_004', 'Before offering help, assess whether the person is taking any steps on their own behalf.', 1),
(29, 'DLCOD_IChanneledMyAngerIntoArt_004', 'Distinguish between providing temporary support and taking on long-term responsibility for another adult\'s well-being.', 2),
(30, 'DLCOD_IChanneledMyAngerIntoArt_004', 'Consider if your \'help\' is preventing the person from facing the natural consequences of their inaction.', 3),
(31, 'DLCOD_IChanneledMyAngerIntoArt_004', 'Offer encouragement and ideas for self-help (their \'Shrinky Dinks\') rather than just doing things for them.', 4),
(32, 'DLCOD_IChanneledMyAngerIntoArt_005', 'Assess the entire family system, not just your role in it. Are there other people who can and should be helping?', 1),
(33, 'DLCOD_IChanneledMyAngerIntoArt_005', 'Give yourself permission to detach from ongoing drama that you cannot solve.', 2),
(34, 'DLCOD_IChanneledMyAngerIntoArt_005', 'Re-engage in your own life and focus on the responsibilities that are truly yours.', 3),
(35, 'DLCOD_IChanneledMyAngerIntoArt_005', 'Recognise that feeling \'odd\' or guilty when changing a long-standing family role is normal, but it doesn\'t mean your decision is wrong.', 4),
(36, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'Reframe flexibility with custody schedules as a form of compassion, not a failure.', 1),
(37, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'When your child is with you, make the most of it with dedicated, positive \'mum/dad time\'.', 2),
(38, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'Actively show enthusiasm for your child\'s relationship with your ex-partner to reduce their stress.', 3),
(39, 'DRLAURA_REFRESHER_HOW_CAN_I_HELP_MY_SON', 'Don\'t let guilt from the past dictate your present parenting decisions; focus on what\'s best for the child now.', 4),
(40, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'Identify your and your partner\'s unique spiritual parenting strengths (e.g., structured vs. spontaneous).', 1),
(41, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'Instead of trying to force one method, discuss how your different approaches can complement each other.', 2),
(42, 'FOCUS_ON_PARENTING_HOW_YOU_BOTH_TEACH_KIDS_ABOUT_GOD', 'Try \'round-robin\' reading for family scripture or story time, where each person reads a small section to keep everyone engaged.', 3),
(43, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'Look beyond the rebellious behaviour to identify the underlying emotion, which is often anger or hurt.', 1),
(44, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'Open a conversation about past events that might still be affecting your teen, like a family move or big life change.', 2),
(45, 'PARENTING_TODAY_TEENS_TEEN_STORIES_REBELLION', 'Recognise that substance use can be a coping mechanism for difficult emotions, not just a desire to party.', 3),
(46, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'After a sibling fight, guide them through apologies and forgiveness.', 1),
(47, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'Institute a \'reconciliation hug\' where they have to hug until both are smiling.', 2),
(48, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'Use physical connection (hugs, holding hands) to help bridge emotional divides after arguments.', 3),
(49, 'RAISING_BOYS_AND_GIRLS_JUSTIN_WHITMEL_EARLEY', 'Model this behaviour with your partner to show that reconciliation is an active, embodied process.', 4),
(50, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'Embrace the chaos of family travel; forgetting things is practically a rite of passage.', 1),
(51, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'Have a laugh about it. It’s better than getting stressed over a lost pair of tweezers.', 2),
(52, 'HAVE_KIDS_THEY_SAID_SOULMATES_OR_IS_DRUGS', 'Maybe, just maybe, do one final sweep of the room before you leave. Or just accept your fate as a benevolent scatterer of goods.', 3),
(53, 'CARE_FEEDING_ONLY_CHILD', 'Focus on building a \'chosen family\' of close friends and community members.', 1),
(54, 'CARE_FEEDING_ONLY_CHILD', 'Be direct with other parents about your desire to foster deeper connections for your child.', 2),
(55, 'CARE_FEEDING_ONLY_CHILD', 'Establish regular, predictable get-togethers with other families to create a sense of stability and belonging.', 3),
(56, 'CARE_FEEDING_ONLY_CHILD', 'Validate your child\'s desire for a sibling while also celebrating the unique strengths of your family unit.', 4),
(57, 'PARENTING_HELL_S10_EP43', 'Beware the sponsored link when buying the toy-of-the-moment online.', 1),
(58, 'PARENTING_HELL_S10_EP43', 'Accept that you will, at some point, buy a disappointing knock-off version of something your child desperately wants.', 2),
(59, 'PARENTING_HELL_S10_EP43', 'Have a good laugh about it, because what else can you do?', 3),
(60, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'Turn a repetitive instruction (like \'brush your teeth\') into a simple, silly song or jingle.', 1),
(61, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'Use a playful tone to shift the energy from a command to a fun, shared activity.', 2),
(62, 'RAISING_BOYS_AND_GIRLS_ZIGGY_MARLEY', 'You don\'t have to be a musician – a simple rhyme or chant is all it takes to make a difference.', 3),
(63, 'drlaura_bad_dad_01', 'Re-evaluate your sense of obligation to a parent who was not there for you.', 1),
(64, 'drlaura_bad_dad_01', 'Recognise that helping is a choice you make, not a duty you owe.', 2),
(65, 'drlaura_bad_dad_01', 'Give yourself permission to step back from a relationship that has historically been one-sided and painful.', 3),
(66, 'drlaura_bad_dad_02', 'Avoid a final, dramatic confrontation which can lead to further manipulation.', 1),
(67, 'drlaura_bad_dad_02', 'Simply wish the person well and then focus your energy on your own life.', 2),
(68, 'drlaura_bad_dad_02', 'Understand that quietly disengaging is a valid and powerful way to protect your peace.', 3),
(69, 'drlaura_bad_dad_02', 'Remind yourself that your decision to step back is justified by their past actions.', 4),
(70, 'drlaura_bad_dad_03', 'Reflect on the difference between being a biological parent and being an active, engaged father.', 1),
(71, 'drlaura_bad_dad_03', 'Understand that your title as \'Dad\' is reinforced by your daily actions and presence.', 2),
(72, 'drlaura_bad_dad_03', 'Teach your children that relationships are built on mutual respect and effort, not just titles.', 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `display_name` varchar(150) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'Europe/London',
  `newsletter_frequency` enum('daily','weekly','monthly') DEFAULT 'weekly',
  `email_verified` tinyint(1) DEFAULT 0,
  `email_verification_token` varchar(255) DEFAULT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `login_count` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_bookmarks`
--

CREATE TABLE `user_bookmarks` (
  `user_id` int(11) NOT NULL,
  `article_id` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `user_bookmarks`
--
DELIMITER $$
CREATE TRIGGER `update_bookmark_count_on_delete` AFTER DELETE ON `user_bookmarks` FOR EACH ROW BEGIN
    UPDATE articles 
    SET bookmark_count = bookmark_count - 1 
    WHERE id = OLD.article_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bookmark_count_on_insert` AFTER INSERT ON `user_bookmarks` FOR EACH ROW BEGIN
    UPDATE articles 
    SET bookmark_count = bookmark_count + 1 
    WHERE id = NEW.article_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_interactions`
--

CREATE TABLE `user_interactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `article_id` varchar(255) DEFAULT NULL,
  `interaction_type` enum('view','bookmark','unbookmark','share','like','unlike') NOT NULL,
  `interaction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interaction_data`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `user_interactions`
--
DELIMITER $$
CREATE TRIGGER `update_view_count_on_view` AFTER INSERT ON `user_interactions` FOR EACH ROW BEGIN
    IF NEW.interaction_type = 'view' THEN
        UPDATE articles 
        SET view_count = view_count + 1 
        WHERE id = NEW.article_id;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `age_groups`
--
ALTER TABLE `age_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_articles_content_type` (`content_type`),
  ADD KEY `idx_articles_urgency` (`urgency`),
  ADD KEY `idx_articles_created_at` (`created_at`),
  ADD KEY `idx_articles_engagement_score` (`engagement_score`),
  ADD KEY `idx_articles_newsletter_priority` (`newsletter_priority`),
  ADD KEY `idx_articles_view_count` (`view_count`),
  ADD KEY `idx_articles_bookmark_count` (`bookmark_count`),
  ADD KEY `idx_articles_engagement` (`view_count`,`bookmark_count`);

--
-- Indexes for table `article_age_groups`
--
ALTER TABLE `article_age_groups`
  ADD PRIMARY KEY (`article_id`,`age_group_id`),
  ADD KEY `age_group_id` (`age_group_id`);

--
-- Indexes for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD PRIMARY KEY (`article_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD PRIMARY KEY (`article_id`,`tag_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `migration_log`
--
ALTER TABLE `migration_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `related_topics`
--
ALTER TABLE `related_topics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `takeaways`
--
ALTER TABLE `takeaways`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_created_at` (`created_at`),
  ADD KEY `idx_users_last_login` (`last_login`),
  ADD KEY `idx_users_active` (`is_active`),
  ADD KEY `idx_users_email_verified` (`email_verified`);

--
-- Indexes for table `user_bookmarks`
--
ALTER TABLE `user_bookmarks`
  ADD PRIMARY KEY (`user_id`,`article_id`),
  ADD KEY `idx_bookmarks_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_bookmarks_article` (`article_id`),
  ADD KEY `idx_bookmarks_favorites` (`user_id`,`is_favorite`);

--
-- Indexes for table `user_interactions`
--
ALTER TABLE `user_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_interactions_user_created` (`user_id`,`created_at`),
  ADD KEY `idx_interactions_article_type` (`article_id`,`interaction_type`),
  ADD KEY `idx_interactions_type_created` (`interaction_type`,`created_at`),
  ADD KEY `idx_interactions_user_article` (`user_id`,`article_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `age_groups`
--
ALTER TABLE `age_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migration_log`
--
ALTER TABLE `migration_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `related_topics`
--
ALTER TABLE `related_topics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `sources`
--
ALTER TABLE `sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `takeaways`
--
ALTER TABLE `takeaways`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_interactions`
--
ALTER TABLE `user_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `article_age_groups`
--
ALTER TABLE `article_age_groups`
  ADD CONSTRAINT `article_age_groups_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_age_groups_ibfk_2` FOREIGN KEY (`age_group_id`) REFERENCES `age_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_categories`
--
ALTER TABLE `article_categories`
  ADD CONSTRAINT `article_categories_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `article_tags`
--
ALTER TABLE `article_tags`
  ADD CONSTRAINT `article_tags_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `article_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `related_topics`
--
ALTER TABLE `related_topics`
  ADD CONSTRAINT `related_topics_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sources`
--
ALTER TABLE `sources`
  ADD CONSTRAINT `sources_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `takeaways`
--
ALTER TABLE `takeaways`
  ADD CONSTRAINT `takeaways_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_bookmarks`
--
ALTER TABLE `user_bookmarks`
  ADD CONSTRAINT `user_bookmarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_bookmarks_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_interactions`
--
ALTER TABLE `user_interactions`
  ADD CONSTRAINT `user_interactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_interactions_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
